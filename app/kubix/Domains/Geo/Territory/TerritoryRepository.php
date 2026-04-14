<?php
namespace App\Kubix\Domains\Geo\Territory;

use App\Models\Territory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/**
 * Repository de Territorios (KUBIX Engine)
 * Responsabilidades:
 * - Búsqueda espacial optimizada (Nombre + Geometría).
 * - Persistencia de jerarquía mediante Materialized Path.
 * - Normalización de identidad para evitar duplicidad.
 */
class TerritoryRepository
{
    protected $model;

    public function __construct(Territory $model)
    {
        $this->model = $model;
    }

// ==================  Funciones Principales  ============================

    /**
     * Crea un territorio garantizando la integridad del Path y Depth.
     */
    public function createFromDiscovery(array $data): Territory
    {
        return DB::transaction(function () use ($data) {
            // 1. Idempotencia: Si ya existe, no hacemos nada
            if ($existing = $this->findStrict($data['name'], $data['type'], $data['parent_id'] ?? null)) {
                return $existing;
            }

            $parent = isset($data['parent_id']) ? Territory::find($data['parent_id']) : null;

            // 2. Creación base (necesitamos el registro para actualizarlo después)
            $territory = Territory::create([
                'type'            => $data['type'],
                'name'            => $data['name'],
                'normalized_name' => $this->normalize($data['name']),
                'slug'            => $this->generateUniqueSlug($data['name']),
                'parent_id'       => $data['parent_id'] ?? null,
                'latitude'        => $data['latitude'] ?? null,
                'longitude'       => $data['longitude'] ?? null,
                'is_active'       => true,
                'code'            => $data['code'] ?? $this->generateFallbackCode($data['name']),
            ]);

            /**
             * 3. LÓGICA DE ÍNDICE RELATIVO (ORO PURO)
             * En lugar de usar el ID de la DB, contamos la posición del hijo respecto al padre.
             */
            if (! $parent) {
                // Es un PAÍS: Su índice es el conteo global de países
                $index = Territory::whereNull('parent_id')->withTrashed()->count();
                $path  = "/{$index}/";
            } else {
                // Es un HIJO: Su índice es el conteo de hermanos bajo el mismo padre
                $index = Territory::where('parent_id', $parent->id)->withTrashed()->count();
                $path  = "{$parent->path}{$index}/";
            }

            $depth = substr_count(trim($path, '/'), '/') + 1;

            // 4. Actualización atómica de Path, Depth y Boundary
            $updateData = [
                'path'  => $path,
                'depth' => $depth,
            ];

            if (! empty($data['boundary']) && $wkt = $this->geojsonToWKT((array) $data['boundary'])) {
                $updateData['boundary'] = DB::raw("ST_GeomFromText('{$wkt}')");
            }

            DB::table('territories')->where('id', $territory->id)->update($updateData);

            return $territory->refresh();
        });
    }

    /**
     * Activa o desactiva un territorio y TODA su descendencia de forma atómica.
     * Ideal para suspender operaciones en países, estados o ciudades completas.
     */
    public function toggleActive(int $id, bool $status): bool
    {
        return DB::transaction(function () use ($id, $status) {
            $territory = Territory::findOrFail($id);

            // Actualizamos el registro actual y todos sus descendientes
            // usando el índice del Path para una ejecución de alta velocidad.
            $affected = DB::table('territories')
                ->where('path', 'like', "{$territory->path}%")
                ->update([
                    'is_active'  => $status,
                    'updated_at' => now(),
                ]);

            Log::info("KUBIX_SECURITY: Cambio de estado masivo", [
                'root_name'      => $territory->name,
                'new_status'     => $status,
                'affected_nodes' => $affected,
            ]);

            return $affected > 0;
        });
    }

//==================  Utilidades para Geometría  ============================

/**
 * Convierte GeoJSON a WKT (Well-Known Text)
 */
    private function geojsonToWKT(array $geojson): ?string
    {
        $type   = $geojson['type'] ?? null;
        $coords = $geojson['coordinates'] ?? [];

        return match ($type) {
            'Point' => count($coords) >= 2 ? "POINT({$coords[0]} {$coords[1]})" : null,
            'Polygon' => $this->polygonToWKT($coords),
            'MultiPolygon' => $this->multiPolygonToWKT($coords),
            default => null,
        };
    }

    /**
     * Procesa MultiPolygons extrayendo el contenido de cada Polygon.
     */
    private function multiPolygonToWKT(array $coordinates): ?string
    {
        $polygons = collect($coordinates)
            ->map(fn($poly) => str_replace('POLYGON', '', $this->polygonToWKT($poly) ?? ''))
            ->filter()
            ->implode(',');

        return $polygons ? "MULTIPOLYGON($polygons)" : null;
    }

    /**
     * Convierte coordenadas de Polygon a WKT.
     */
    private function polygonToWKT(array $coordinates): ?string
    {
        $rings = [];
        foreach ($coordinates as $ring) {
            $points = [];
            foreach ($ring as $coord) {
                if (is_array($coord) && count($coord) >= 2) {
                    $points[] = "{$coord[0]} {$coord[1]}";
                }
            }

            if (! empty($points)) {
                // Validación de integridad: Cerrar el anillo si es necesario
                if (reset($points) !== end($points)) {
                    $points[] = reset($points);
                }
                $rings[] = "(" . implode(",", $points) . ")";
            }
        }

        return ! empty($rings) ? "POLYGON(" . implode(",", $rings) . ")" : null;
    }

//==================  Utilidades para Normalización e Identidad  ============================

    /**
     * Normaliza nombres para comparaciones consistentes.
     */
    private function normalize(string $name): string
    {
        // Eliminamos espacios, pasamos a mayúsculas y quitamos acentos si es necesario
        $name = trim($name);
        $name = mb_strtoupper($name, 'UTF-8');

        // Opcional: Quitar acentos para máxima compatibilidad
        $search  = ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'];
        $replace = ['A', 'E', 'I', 'O', 'U', 'N'];

        return str_replace($search, $replace, $name);
    }

    /**
     * Genera un slug único para el territorio.
     */
    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = str()->slug($name); // Usamos el helper de Laravel
        $slug     = $baseSlug;
        $count    = 1;

        // Verificamos si existe en la tabla (incluyendo los eliminados por SoftDeletes)
        while (Territory::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-" . $count++;
        }

        return $slug;
    }

/**
 * Genera un código interno si no viene uno de la fuente de datos.
 */
    private function generateFallbackCode(string $name): string
    {
        return 'TR-' . substr(md5($name . time()), 0, 8);
    }

//==================  Búsquedas Especializadas  ============================

/**
 * Búsqueda ESTRICTA por Jerarquía para el proceso de Discovery.
 * * Optimizado para:
 * 1. Evitar duplicidad de lógica de normalización.
 * 2. Legibilidad con sintaxis de arreglos.
 */
    public function findStrict(string $name, string $type, ?int $parentId = null): ?Territory
    {
        return Territory::where([
            'type'            => $type,
            'parent_id'       => $parentId,
            'normalized_name' => $this->normalize($name),
        ])->first();
    }

    /**
     * El "Filtro Maestro" para el Pre-login.
     * Optimizado con Bindings para seguridad y soporte de índices espaciales.
     */
    public function findByCoordsAndName(?string $name, float $lat, float $lon): ?Territory
    {
        if (! $name) {
            return null;
        }

        return Territory::where('normalized_name', $this->normalize($name))
            ->where('type', 'neighborhood')
            ->whereRaw(
                "ST_Contains(boundary, ST_GeomFromText(?))",
                ["POINT($lon $lat)"]
            )
            ->first();
    }

   /**
     * Busca un territorio por su path exacto (Ej: /1/1/1/1/)
     * * @param string $path
     * @return Territory|null
     */
    public function findByPath(string $path): ?Territory
    {
        Log::debug("REPOSITORY: Buscando Territorio por Path", ['path_buscado' => $path]);

        $territory = Territory::where('path', $path)->first();

        if (!$territory) {
            Log::warning("REPOSITORY: Territorio NO encontrado para el Path", ['path' => $path]);
        } else {
            Log::info("REPOSITORY: Territorio Localizado", [
                'id' => $territory->id, 
                'nombre' => $territory->name
            ]);
        }

        return $territory;
    }

} 


