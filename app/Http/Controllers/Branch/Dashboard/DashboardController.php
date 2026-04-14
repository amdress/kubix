<?php

namespace App\Kubix\Features\Branch\Dashboard;

use App\Http\Controllers\Controller;
use App\Kubix\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * DashboardController
 *
 * Endpoint único para el dashboard territorial.
 *
 * GET /api/v1/dashboard?branch_id=     → global (todos los países)
 * GET /api/v1/dashboard?branch_id=1    → Brasil (muestra estados)
 * GET /api/v1/dashboard?branch_id=2    → Paraná (muestra ciudades)
 * GET /api/v1/dashboard?branch_id=3    → Curitiba (muestra barrios)
 * GET /api/v1/dashboard?branch_id=4    → Pinheirinho (muestra empresas)
 *
 * AUTORIZACIÓN:
 *   superadmin    → puede consultar cualquier branch_id
 *   branch_manager → solo puede consultar su branch y sus hijos (TODO: policy)
 */
class DashboardController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected DashboardService $dashboardService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $branchId = $request->query('branch_id')
                ? (int) $request->query('branch_id')
                : null;

            $data = $this->dashboardService->getData($branchId);

            return $this->successResponse($data);

        } catch (\Throwable $e) {
            return $this->handleException($e, 'DashboardController@index', [
                'branch_id' => $request->query('branch_id'),
            ]);
        }
    }
}