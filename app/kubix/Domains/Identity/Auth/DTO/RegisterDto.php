<?php

declare(strict_types=1);

namespace App\Kubix\Domains\Identity\Auth\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Unique;

class RegisterDto extends Data
{
    public function __construct(
        #[Required]
        public string $name,

        #[Required, Email, Unique('users', 'email')]
        public string $email,

        #[Required, Min(8), Confirmed]
        public string $password,

        public string $password_confirmation,

        #[Required]
        public string $current_path,

        public ?float $lat = null,
        public ?float $lon = null,
        public ?string $phone = null,
        public ?string $device_name = 'web-access',
    ) {}

    public static function messages(): array
    {
        return [
            'name.required'         => 'El nombre es obligatorio.',
            'email.required'        => 'El correo es obligatorio.',
            'email.unique'          => 'Este correo ya existe.',
            'password.confirmed'    => 'Las contraseñas no coinciden.',
            'current_path.required' => 'Falta el contexto geográfico (Path).',
        ];
    }
}