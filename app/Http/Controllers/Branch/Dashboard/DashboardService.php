<?php

namespace App\Kubix\Features\Branch\Dashboard;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Affiliation;
use Illuminate\Support\Facades\DB;

/**
 * DashboardService
 *
 * Calcula las métricas del dashboard territorial para cualquier nivel.
 *
 * RESPONSABILIDAD:
 *   Dado un branch_id (o null para global), retorna:
 *     - kpis       → métricas numéricas del territorio
 *     - mapPoints  → coordenadas + boundary para pintar en el mapa
 *     - distribution → hijos directos con su revenue (para el gráfico)
 *
 * JERARQUÍA:
 *   null          → global (todas las branches raíz)
 *   country       → muestra sus estados hijos
 *   state         → muestra sus ciudades hijas
 *   city          → muestra sus barrios hijos
 *   neighborhood  → muestra sus empresas
 *
 * MÉTRICAS:
 *   users    → COUNT affiliations activas en el territorio y sus hijos
 *   revenue  → SUM payments confirmados en el territorio y sus hijos
 *   mrr      → SUM subscriptions activas mensuales en el territorio y sus hijos
 *   branches → COUNT branches activas hijas
 */
class DashboardService
{
    // =========================================================================
    // ENTRY POINT
    // =========================================================================

    /**
     * Retorna los datos completos del dashboard para un territorio.
     *
     * @param int|null $branchId  null = vista global
     * @return array
     */
    public function getData(?int $branchId): array
    {
        if ($branchId === null) {
            return $this->buildGlobal();
        }

        $branch = Branch::with('territory')->find($branchId);

        if (! $branch) {
            throw new \DomainException("Branch #{$branchId} no encontrada.");
        }

        return $this->buildForBranch($branch);
    }

    // =========================================================================
    // GLOBAL — Sin branch_id
    // =========================================================================

    /**
     * Vista global — muestra todas las branches raíz (países).
     */
    private function buildGlobal(): array
    {
        $roots = Branch::with('territory')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->get();

        // Todos los IDs del sistema para calcular totales
        $allBranchIds = Branch::where('is_active', true)->pluck('id')->toArray();

        $kpis = [
            $this->kpi('total-users',    'Total Usuarios',   $this->countUsers($allBranchIds),              'users'),
            $this->kpi('total-branches', 'Branches Activas', Branch::where('is_active', true)->count(),     'branches'),
            $this->kpi('total-revenue',  'Revenue Total',    $this->sumRevenue($allBranchIds),              'currency'),
            $this->kpi('total-mrr',      'MRR Total',        $this->sumMrr($allBranchIds),                  'currency'),
        ];

        $mapPoints = $roots->map(fn ($b) => $this->toMapPoint($b))->values()->toArray();

        $distribution = $roots->map(fn ($b) => [
            'name'  => $b->name,
            'sub'   => 'País',
            'value' => $this->sumRevenue($this->collectBranchIds($b->id)),
        ])->sortByDesc('value')->values()->toArray();

        return compact('kpis', 'mapPoints', 'distribution');
    }

    // =========================================================================
    // POR BRANCH
    // =========================================================================

    /**
     * Vista de una Branch específica — muestra sus hijos directos.
     */
    private function buildForBranch(Branch $branch): array
    {
        // IDs del territorio completo (branch + todos sus descendientes)
        $allIds = $this->collectBranchIds($branch->id);

        // Hijos directos
        $children = Branch::with('territory')
            ->where('parent_id', $branch->id)
            ->where('is_active', true)
            ->get();

        $kpis = [
            $this->kpi('users',    'Usuarios',          $this->countUsers($allIds),                      'users'),
            $this->kpi('branches', 'Branches Activas',  $children->count(),                              'branches'),
            $this->kpi('revenue',  'Revenue',           $this->sumRevenue($allIds),                      'currency'),
            $this->kpi('mrr',      'MRR',               $this->sumMrr($allIds),                          'currency'),
        ];

        // Nivel neighborhood → muestra empresas en el mapa en lugar de branches hijas
        if ($branch->isNeighborhood()) {
            $mapPoints    = $this->getCompanyMapPoints($branch->id);
            $distribution = $this->getCompanyDistribution($branch->id);
        } else {
            $mapPoints    = $children->map(fn ($b) => $this->toMapPoint($b))->values()->toArray();
            $distribution = $children->map(fn ($b) => [
                'name'  => $b->name,
                'sub'   => $branch->childLevelLabel(),
                'value' => $this->sumRevenue($this->collectBranchIds($b->id)),
            ])->sortByDesc('value')->values()->toArray();
        }

        return compact('kpis', 'mapPoints', 'distribution');
    }

    // =========================================================================
    // MÉTRICAS
    // =========================================================================

    /**
     * COUNT de usuarios afiliados activos en un conjunto de branches.
     * Un usuario puede estar en múltiples branches — contamos únicos.
     */
    private function countUsers(array $branchIds): int
    {
        return Affiliation::whereIn('affiliatable_id', $branchIds)
            ->where('affiliatable_type', Branch::class)
            ->where('is_active', true)
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * SUM de payments confirmados en un conjunto de branches.
     */
    private function sumRevenue(array $branchIds): float
    {
        return (float) Payment::whereIn('branch_id', $branchIds)
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * SUM de subscripciones activas mensuales — MRR.
     */
    private function sumMrr(array $branchIds): float
    {
        return (float) Subscription::whereIn('branch_id', $branchIds)
            ->where('status', 'active')
            ->whereHas('plan', fn ($q) => $q->where('billing_cycle', 'monthly'))
            ->sum('price_paid');
    }

    // =========================================================================
    // TREND — Comparar período actual vs anterior
    // =========================================================================

    /**
     * Calcula el % de cambio entre el período actual y el anterior.
     * Usado para mostrar la flecha de tendencia en los KPIs.
     *
     * @param array  $branchIds
     * @param string $period  'week' | 'month'
     * @return float  porcentaje de cambio (+12.5 = subió 12.5%)
     */
    private function calculateTrend(array $branchIds, string $period = 'week'): float
    {
        $now  = now();
        $unit = $period === 'week' ? 7 : 30;

        $current = Payment::whereIn('branch_id', $branchIds)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$now->copy()->subDays($unit), $now])
            ->sum('amount');

        $previous = Payment::whereIn('branch_id', $branchIds)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$now->copy()->subDays($unit * 2), $now->copy()->subDays($unit)])
            ->sum('amount');

        if ($previous == 0) return $current > 0 ? 100.0 : 0.0;

        return round((($current - $previous) / $previous) * 100, 1);
    }

    // =========================================================================
    // MAP POINTS
    // =========================================================================

    /**
     * Convierte una Branch en un punto del mapa.
     * Incluye el boundary GeoJSON del territory para pintar el polígono.
     */
    private function toMapPoint(Branch $branch): array
    {
        $territory = $branch->territory;

        return [
            'id'       => $branch->id,
            'label'    => $branch->name,
            'code'     => $branch->code,
            'lat'      => $territory?->latitude,
            'lng'      => $territory?->longitude,
            'boundary' => $territory?->boundary, // GeoJSON polygon
            'type'     => 'area',
        ];
    }

    /**
     * Empresas como puntos del mapa — usado en nivel neighborhood.
     */
    private function getCompanyMapPoints(int $branchId): array
    {
        return Company::with('addresses')
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->get()
            ->map(function ($company) {
                $address = $company->addresses->first();
                return [
                    'id'    => $company->id,
                    'label' => $company->displayName(),
                    'lat'   => $address?->latitude,
                    'lng'   => $address?->longitude,
                    'type'  => 'business',
                ];
            })
            ->values()
            ->toArray();
    }

    // =========================================================================
    // DISTRIBUTION
    // =========================================================================

    /**
     * Distribución de empresas — usado en nivel neighborhood.
     */
    private function getCompanyDistribution(int $branchId): array
    {
        $companies = Company::where('branch_id', $branchId)
            ->where('is_active', true)
            ->get();

        return $companies->map(function ($company) {
            return [
                'name'  => $company->displayName(),
                'sub'   => $company->type === 'formal' ? 'Formal' : 'Informal',
                'value' => $company->totalPaid(),
            ];
        })->sortByDesc('value')->values()->toArray();
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Construye un KPI estandarizado.
     */
    private function kpi(string $id, string $title, float|int $value, string $type): array
    {
        return [
            'id'    => $id,
            'title' => $title,
            'value' => $value,
            'type'  => $type, // 'currency' | 'users' | 'branches'
        ];
    }

    /**
     * Recolecta recursivamente todos los IDs de una Branch y sus descendientes.
     * Usado para calcular métricas agregadas del territorio completo.
     *
     * Ej: Paraná → [Paraná, Curitiba, Pinheirinho, Centro, SJP, Centro SJP]
     */
    private function collectBranchIds(int $branchId): array
    {
        $ids      = [$branchId];
        $children = Branch::where('parent_id', $branchId)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->collectBranchIds($childId));
        }

        return array_unique($ids);
    }
}