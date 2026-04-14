<?php

use App\Http\Controllers\Auth\AuthController;
// use App\Kubix\Features\Branch\Dashboard\DashboardController;
// use App\Kubix\Features\Branch\Management\BranchManagementController;
// use App\Kubix\Features\Company\Management\CompanyManagementController;
use App\Http\Controllers\Territory\Management\TerritoryManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SANCTUM CSRF — pública
|--------------------------------------------------------------------------
*/
Route::post('/sanctum/csrf-cookie', fn() => response()->json(['ok' => true]))
    ->name('sanctum.csrf-cookie');

/*
|--------------------------------------------------------------------------
| PUBLIC V1 — sin autenticación
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Health check
    Route::get('/ping', function () {
        return response()->json([
            'status'  => 'success',
            'message' => 'API conectada correctamente.',
            'data'    => [
                'ok'        => true,
                'timestamp' => now(),
            ],
            'errors'  => null,
        ]);
    });

    // Auth endpoints
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    // Territories — bootstrap
    // Route::get('territories/countries', [TerritoryManagementController::class, 'getCountries']);
    Route::get('territories/check-availability', [TerritoryManagementController::class, 'checkAvailability']);

    Route::post('territories/findSuggests', [TerritoryManagementController::class, 'findSuggests']);
    Route::post('territories/resolve-selection', [TerritoryManagementController::class, 'resolveSelection']);

});

/*
|--------------------------------------------------------------------------
| PRIVATE V1 — requiere auth:sanctum
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // Auth
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // // Dashboard
    // Route::get('dashboard', [DashboardController::class, 'index']);

    // // Branches
    // Route::prefix('branches')->group(function () {
    //     Route::get('/', [BranchManagementController::class, 'index']);
    //     Route::post('/', [BranchManagementController::class, 'store']);
    //     Route::get('{id}', [BranchManagementController::class, 'show']);
    //     Route::put('{id}', [BranchManagementController::class, 'update']);
    //     Route::delete('{id}', [BranchManagementController::class, 'destroy']);
    //     Route::post('{id}/assign-manager', [BranchManagementController::class, 'assignManager']);
    //     Route::post('{id}/assign-staff', [BranchManagementController::class, 'assignStaff']);
    // });

    // Companies
    // Route::prefix('companies')->group(function () {
    //     Route::get('/', [CompanyManagementController::class, 'index']);
    //     Route::post('/', [CompanyManagementController::class, 'store']);
    //     Route::get('{id}', [CompanyManagementController::class, 'show']);
    //     Route::put('{id}', [CompanyManagementController::class, 'update']);
    //     Route::delete('{id}', [CompanyManagementController::class, 'destroy']);
    //     Route::post('{id}/assign-staff', [CompanyManagementController::class, 'assignStaff']);
    // });

    // Territories — admin
    // Route::prefix('territories')->group(function () {
    //     Route::get('/', [TerritoryManagementController::class, 'index']);
    //     Route::get('{id}', [TerritoryManagementController::class, 'show']);
    //     Route::post('/', [TerritoryManagementController::class, 'store']);
    //     Route::put('{id}', [TerritoryManagementController::class, 'update']);
    //     Route::delete('{id}', [TerritoryManagementController::class, 'destroy']);
    // });

});
