<?php

namespace App\Kubix\Core\Traits;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * ApiResponse
 *
 * Trait que estandariza todas las respuestas JSON de la API.
 * Úsalo en cualquier Controller para garantizar consistencia
 * en el formato de respuesta a través de todos los endpoints.
 *
 * Formato base — SIEMPRE el mismo, sea éxito o error:
 * {
 *   "status":  "success" | "error",
 *   "message": "Texto legible para el usuario",
 *   "data":    {...} | null,
 *   "errors":  [...] | null   → solo en errores de validación
 * }
 *
 * El frontend solo pinta. No procesa, no transforma, no interpreta códigos.
 */
trait ApiResponse
{
    // =========================================================================
    // RESPUESTAS EXITOSAS
    // =========================================================================

    /**
     * Respuesta exitosa genérica — 200.
     */
    protected function successResponse(mixed $data, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
        ], $code);
    }

    /**
     * Respuesta de creación exitosa — 201.
     */
    protected function createdResponse(mixed $data, ?string $message = null): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Respuesta vacía — 204.
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    // =========================================================================
    // RESPUESTAS DE ERROR
    // =========================================================================

    /**
     * Respuesta de error genérica.
     */
    protected function errorResponse(?string $message = null, int $code = 500, ?array $errors = null): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message ?? 'Error interno del servidor',
            'data'    => null,
            'errors'  => $errors,
        ], $code);
    }

    /**
     * Recurso no encontrado — 404.
     */
    protected function notFoundResponse(?string $message = 'Recurso no encontrado'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Error de validación — 422.
     */
    protected function validationErrorResponse(?string $message = 'Datos inválidos', ?array $errors = null): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Error de autorización — 403.
     */
    protected function forbiddenResponse(?string $message = 'No autorizado'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    // =========================================================================
    // MANEJO CENTRALIZADO DE EXCEPCIONES
    // =========================================================================

    /**
     * Transforma cualquier excepción en una respuesta JSON limpia y legible.
     *
     * El frontend recibe siempre el mismo formato sin importar el tipo de error.
     * Solo pinta el mensaje — no procesa, no interpreta códigos, no transforma.
     *
     * Mapeo de excepciones:
     *   ValidationException → 422 con errors por campo (para pintar en inputs)
     *   DomainException     → 422 con mensaje de negocio legible
     *   QueryException      → 422 con mensaje legible según código SQL
     *   Throwable           → 500 con mensaje genérico + log detallado interno
     */
    protected function handleException(\Throwable $e, string $context = '', array $logExtra = []): JsonResponse
    {
        // ── Errores de validación de Laravel ─────────────────────────────────
        // Campos requeridos, formatos inválidos, exists fallidos, etc.
        if ($e instanceof ValidationException) {
            return $this->validationErrorResponse(
                message: 'Los datos enviados no son válidos.',
                errors:  $e->errors(),
            );
        }

        // ── Errores de negocio — reglas del dominio ───────────────────────────
        // Branch inactiva, usuario ya es manager, CNPJ requerido, etc.
        if ($e instanceof \DomainException) {
            return $this->validationErrorResponse(
                message: $e->getMessage(),
                errors:  null,
            );
        }

        // ── Errores de base de datos ──────────────────────────────────────────
        // Duplicados, FK violations, campos nulos requeridos, etc.
        if ($e instanceof QueryException) {
            $message = $this->resolveQueryExceptionMessage($e);

            Log::warning("ApiResponse@handleException [{$context}]: QueryException", [
                'sql_code' => $e->getCode(),
                'message'  => $e->getMessage(),
                'extra'    => $logExtra,
            ]);

            return $this->validationErrorResponse($message);
        }

        // ── Error inesperado — log completo, mensaje genérico al frontend ─────
        Log::error("ApiResponse@handleException [{$context}]: error inesperado", [
            'error' => $e->getMessage(),
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
            'extra' => $logExtra,
        ]);

        return $this->errorResponse('Ocurrió un error inesperado. Por favor intenta de nuevo.');
    }

    // =========================================================================
    // PRIVADO
    // =========================================================================

    /**
     * Convierte un QueryException en un mensaje legible para el usuario.
     *
     * Los códigos SQLSTATE son estándar y predecibles.
     * El frontend recibe un mensaje claro sin exponer detalles técnicos.
     */
    private function resolveQueryExceptionMessage(QueryException $e): string
    {
        $sqlCode = $e->getCode();

        return match ($sqlCode) {
            // Duplicate entry — unique constraint violation
            '23000' => 'Este registro ya existe. Verifica los datos e intenta de nuevo.',
            // FK constraint — referencia a registro inexistente
            '23503' => 'El registro relacionado no existe.',
            // NOT NULL violation
            '23502' => 'Faltan campos requeridos.',
            // Data too long
            '22001' => 'Uno o más campos exceden el tamaño máximo permitido.',
            default => 'Error al procesar los datos. Por favor intenta de nuevo.',
        };
    }
}