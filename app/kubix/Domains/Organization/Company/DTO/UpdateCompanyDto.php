<?php

namespace App\Kubix\Domains\Organization\Company\DTO;

/**
 * UpdateCompanyDto
 *
 * Transporta los datos validados del frontend para actualizar una Company.
 *
 * Solo los campos que el usuario puede cambiar después de la creación.
 * branch_id y type NO son actualizables — son datos estructurales.
 * cnpj puede actualizarse solo si la empresa pasa de informal a formal.
 */
class UpdateCompanyDto
{
    public function __construct(
        public readonly ?string $name        = null,
        public readonly ?string $tradeName   = null,
        public readonly ?string $cnpj        = null,
        public readonly ?string $email       = null,
        public readonly ?string $phone       = null,
        public readonly ?array  $branding    = null,
        public readonly ?array  $socialLinks = null,
        public readonly ?bool   $isActive    = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:        $data['name']         ?? null,
            tradeName:   $data['trade_name']   ?? null,
            cnpj:        $data['cnpj']         ?? null,
            email:       $data['email']        ?? null,
            phone:       $data['phone']        ?? null,
            branding:    $data['branding']     ?? null,
            socialLinks: $data['social_links'] ?? null,
            isActive:    $data['is_active']    ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'         => $this->name,
            'trade_name'   => $this->tradeName,
            'cnpj'         => $this->cnpj,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'branding'     => $this->branding,
            'social_links' => $this->socialLinks,
            'is_active'    => $this->isActive,
        ], fn ($value) => ! is_null($value));
    }
}