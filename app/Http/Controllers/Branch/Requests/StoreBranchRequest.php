<?php

namespace App\Kubix\Features\Branch\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Validación de autorización en el controller si es necesario
    }

    public function rules(): array
    {
        return [
            // Territory
            'territory'                => ['required', 'array'],
            'territory.country'        => ['required', 'string', 'max:100'],
            'territory.state'          => ['nullable', 'string', 'max:100'],
            'territory.city'           => ['nullable', 'string', 'max:100'],
            'territory.neighborhood'   => ['nullable', 'string', 'max:100'],

            // Branch data
            'branchData'               => ['required', 'array'],
            'branchData.email'         => ['nullable', 'email', 'max:255'],
            'branchData.phone'         => ['nullable', 'string', 'max:20'],
            'branchData.branding'      => ['nullable', 'array'],
            'branchData.branding.primary_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'branchData.branding.logo' => ['nullable', 'url'],
            'branchData.branding.watermark' => ['nullable', 'url'],
            'branchData.branding.splash_image' => ['nullable', 'url'],
            'branchData.branding.display_name' => ['nullable', 'string', 'max:255'],

            // Branch física
            'branchPhysical'           => ['required', 'boolean'],
            'branchAddress'            => ['nullable', 'array'],
            'branchAddress.zip_code'   => ['nullable', 'string', 'max:20'],
            'branchAddress.street'     => ['nullable', 'string', 'max:255'],
            'branchAddress.number'     => ['nullable', 'string', 'max:20'],
            'branchAddress.complement' => ['nullable', 'string', 'max:255'],
            'branchAddress.neighborhood' => ['nullable', 'string', 'max:100'],
            'branchAddress.city'       => ['nullable', 'string', 'max:100'],
            'branchAddress.state'      => ['nullable', 'string', 'max:100'],
            'branchAddress.country'    => ['nullable', 'string', 'max:100'],
            'branchAddress.is_primary' => ['nullable', 'boolean'],
            'branchAddress.reference'  => ['nullable', 'string', 'max:255'],

            // Manager
            'hasManager'               => ['required', 'boolean'],
            'manager'                  => ['nullable', 'array'],
            'manager.email'            => ['nullable', 'email', 'max:255'],
            'manager.password'         => ['nullable', 'string', 'min:8'],
            'manager.cpf'              => ['nullable', 'string', 'max:20'],
            'manager.phone'            => ['nullable', 'string', 'max:20'],
            'manager.avatar'           => ['nullable', 'url'],
            'manager.document'         => ['nullable', 'string', 'max:255'],

            // Manager address
            'hasManagerAddress'        => ['required', 'boolean'],
            'managerAddress'           => ['nullable', 'array'],
            'managerAddress.zip_code'  => ['nullable', 'string', 'max:20'],
            'managerAddress.street'    => ['nullable', 'string', 'max:255'],
            'managerAddress.number'    => ['nullable', 'string', 'max:20'],
            'managerAddress.complement' => ['nullable', 'string', 'max:255'],
            'managerAddress.neighborhood' => ['nullable', 'string', 'max:100'],
            'managerAddress.city'      => ['nullable', 'string', 'max:100'],
            'managerAddress.state'     => ['nullable', 'string', 'max:100'],
            'managerAddress.country'   => ['nullable', 'string', 'max:100'],
            'managerAddress.is_primary' => ['nullable', 'boolean'],
            'managerAddress.reference' => ['nullable', 'string', 'max:255'],
        ];
    }
}