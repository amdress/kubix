<?php

declare(strict_types=1);

namespace App\Kubix\Domains\Identity\Auth\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;

class LoginDto extends Data
{
    public function __construct(
        #[Required, Email]
        public string $email,

        #[Required]
        public string $password,

        #[Required]
        public string $current_path,

        public ?float $lat = null,
        public ?float $lon = null,
        public ?string $device_name = 'web-access',
    ) {}

    public static function messages(): array
    {
        return [
            'email.required'        => 'El correo es obligatorio.',
            'email.email'           => 'El formato del correo no es válido.',
            'password.required'     => 'La contraseña es obligatoria.',
            'current_path.required' => 'Falta el contexto geográfico (Path).',
        ];
    }
}