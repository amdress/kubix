<?php

namespace App\Kubix\Domains\Organization\Company\DTO;

/**
 * CreateCompanyDto
 *
 * Transporta los datos validados del frontend para crear una Company.
 *
 * QUIÉN LO CONSTRUYE:
 *   CompanyManagementController — después de validar el request.
 *
 * LO QUE VIENE DEL FRONTEND:
 *   - branch_id   → territorio elegido por el usuario via selects
 *   - type        → informal (default) o formal
 *   - name        → nombre legal del negocio
 *   - trade_name  → nombre comercial visible (opcional)
 *   - cnpj        → requerido solo si type = formal
 *   - email       → contacto del negocio (opcional)
 *   - phone       → teléfono del negocio (opcional)
 *   - branding    → primary_color, logo, watermark
 *   - social_links → instagram, facebook, whatsapp, x
 *   - hasAddress  → si el negocio tiene dirección física
 */
class CreateCompanyDto
{
    public function __construct(
        public readonly int     $branchId,
        public readonly string  $type,
        public readonly string  $name,
        public readonly ?string $tradeName    = null,
        public readonly ?string $cnpj         = null,
        public readonly ?string $email        = null,
        public readonly ?string $phone        = null,
        public readonly ?array  $branding     = null,
        public readonly ?array  $socialLinks  = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            branchId:    $data['branch_id'],
            type:        $data['type']        ?? 'informal',
            name:        $data['name'],
            tradeName:   $data['trade_name']  ?? null,
            cnpj:        $data['cnpj']        ?? null,
            email:       $data['email']       ?? null,
            phone:       $data['phone']       ?? null,
            branding:    $data['branding']    ?? null,
            socialLinks: $data['social_links'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'branch_id'    => $this->branchId,
            'type'         => $this->type,
            'name'         => $this->name,
            'trade_name'   => $this->tradeName,
            'cnpj'         => $this->cnpj,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'branding'     => $this->branding,
            'social_links' => $this->socialLinks,
        ];
    }
}