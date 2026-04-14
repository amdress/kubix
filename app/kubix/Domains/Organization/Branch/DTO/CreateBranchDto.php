<?php
namespace App\Kubix\Domains\Organization\Branch\DTO;

use Spatie\LaravelData\Data;

/**
 * CreateBranchDto
 *
 * DTO de entrada para la creación de una Branch.
 * Solo valida los campos que el frontend envía:
 * - email, phone, branding
 *
 * Campos generados automáticamente:
 * - name, slug, code (desde territory)
 * - territory_id, territory_level, territory_path (desde territory)
 * - is_active, activated_at (por defecto en creación)
 */
class CreateBranchDto extends Data
{
    public function __construct(
        public ?string $email = null,
        public ?string $phone = null,
        public ?array $branding = null,
    ) {
        // Asegurar que branding sea un array si viene
        if ($this->branding === null) {
            $this->branding = [];
        }
    }

    public static function rules(): array
    {
        return [
            'email'                  => ['nullable', 'email', 'max:255'],
            'phone'                  => ['nullable', 'string', 'max:20'],
            'branding'               => ['nullable', 'array'],
            'branding.primary_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'branding.logo'          => ['nullable', 'url'],
            'branding.watermark'     => ['nullable', 'url'],
            'branding.splash_image'  => ['nullable', 'url'],
            'branding.display_name'  => ['nullable', 'string', 'max:255'],
        ];
    }
}
