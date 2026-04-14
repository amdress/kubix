<?php

namespace App\Kubix\Features\Company\Management;

use App\Http\Controllers\Controller;
use App\Kubix\Common\Address\DTO\CreateAddressDto;
use App\Kubix\Core\Traits\ApiResponse;
use App\Kubix\Domains\Organization\Company\CompanyService;
use App\Kubix\Domains\Organization\Company\DTO\CreateCompanyDto;
use App\Kubix\Domains\Organization\Company\DTO\UpdateCompanyDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyManagementController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CompanyService $companyService,
    ) {}

    // =========================================================================
    // CREACIÓN
    // =========================================================================

    /**
     * Registra una nueva empresa.
     * El usuario autenticado se afilia automáticamente como business_owner.
     *
     * POST /api/v1/companies
     *
     * Payload:
     * {
     *   "companyData": {
     *     "branch_id": 5,
     *     "type": "informal",
     *     "name": "Helados Don Juan",
     *     "trade_name": "Don Juan",
     *     "email": "donjuan@gmail.com",
     *     "phone": "92999999999",
     *     "cnpj": null,
     *     "branding": { "primary_color": "#ff6b6b", "logo": null, "watermark": null },
     *     "social_links": { "instagram": null, "facebook": null, "whatsapp": null, "x": null }
     *   },
     *   "hasAddress": false,
     *   "companyAddress": {
     *     "zip_code": null, "street": null, "number": null, "complement": null,
     *     "neighborhood": null, "city": null, "state": null, "country": null,
     *     "is_primary": true, "reference": null
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();

            Validator::validate($payload, [
                'companyData'              => ['required', 'array'],
                'companyData.branch_id'    => ['required', 'integer'],
                'companyData.type'         => ['required', 'in:informal,formal'],
                'companyData.name'         => ['required', 'string', 'max:150'],
                'companyData.trade_name'   => ['nullable', 'string', 'max:150'],
                'companyData.cnpj'         => ['nullable', 'string', 'max:20'],
                'companyData.email'        => ['nullable', 'email'],
                'companyData.phone'        => ['nullable', 'string', 'max:20'],
                'companyData.branding'     => ['nullable', 'array'],
                'companyData.social_links' => ['nullable', 'array'],
                'hasAddress'               => ['required', 'boolean'],
                'companyAddress'           => ['required', 'array'],
            ]);

            $companyDto = CreateCompanyDto::fromArray($payload['companyData']);

            $addressDto = $payload['hasAddress']
                ? CreateAddressDto::from($payload['companyAddress'])
                : null;

            $company = $this->companyService->create(
                owner:      $request->user(),
                dto:        $companyDto,
                hasAddress: $payload['hasAddress'],
                addressDto: $addressDto,
            );

            return $this->createdResponse($company, 'Empresa registrada exitosamente');

        } catch (\Throwable $e) {
            return $this->handleException($e, 'CompanyManagementController@store');
        }
    }

    // =========================================================================
    // ASIGNACIÓN DE STAFF
    // =========================================================================

    /**
     * Asigna un business_staff a una Company existente.
     * business_owner de esa Company o superadmin pueden ejecutar esta acción.
     *
     * POST /api/v1/companies/{id}/assign-staff
     *
     * Payload: { "user_id": 7 }
     */
    public function assignStaff(Request $request, int $id): JsonResponse
    {
        try {
            Validator::validate($request->all(), [
                'user_id' => ['required', 'integer', 'exists:users,id'],
            ]);

            $this->companyService->assignStaff(
                companyId: $id,
                userId:    $request->integer('user_id'),
            );

            return $this->successResponse(null, 'Staff asignado exitosamente');

        } catch (\Throwable $e) {
            return $this->handleException($e, 'CompanyManagementController@assignStaff', [
                'company_id' => $id,
            ]);
        }
    }

    // =========================================================================
    // ACTUALIZACIÓN
    // =========================================================================

    /**
     * Actualiza los datos de una Company existente.
     *
     * PUT /api/v1/companies/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            Validator::validate($request->all(), [
                'name'         => ['nullable', 'string', 'max:150'],
                'trade_name'   => ['nullable', 'string', 'max:150'],
                'cnpj'         => ['nullable', 'string', 'max:20'],
                'email'        => ['nullable', 'email'],
                'phone'        => ['nullable', 'string', 'max:20'],
                'branding'     => ['nullable', 'array'],
                'social_links' => ['nullable', 'array'],
                'is_active'    => ['nullable', 'boolean'],
            ]);

            $company = $this->companyService->findById($id);

            if (! $company) {
                return $this->notFoundResponse('Empresa no encontrada');
            }

            $dto     = UpdateCompanyDto::fromArray($request->all());
            $company = $this->companyService->update($company, $dto);

            return $this->successResponse($company, 'Empresa actualizada exitosamente');

        } catch (\Throwable $e) {
            return $this->handleException($e, 'CompanyManagementController@update', [
                'company_id' => $id,
            ]);
        }
    }

    // =========================================================================
    // ELIMINACIÓN
    // =========================================================================

    /**
     * Elimina una Company (soft delete).
     *
     * DELETE /api/v1/companies/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $company = $this->companyService->findById($id);

            if (! $company) {
                return $this->notFoundResponse('Empresa no encontrada');
            }

            $this->companyService->delete($company);

            return $this->noContentResponse();

        } catch (\Throwable $e) {
            return $this->handleException($e, 'CompanyManagementController@destroy', [
                'company_id' => $id,
            ]);
        }
    }
}