<?php

namespace App\Kubix\Domains\Identity\User\DTO;

use Spatie\LaravelData\Data;

class CreateUserDto extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $cpf = null,
        public ?string $phone = null,
        public ?string $avatar = null,      // URL o nombre de archivo enviado
        public ?array $document = null,     // array de referencias a archivos, no el archivo físico
    ) {
        // Normalizamos el CPF eliminando puntos y guiones
        if ($this->cpf) {
            $this->cpf = preg_replace('/\D/', '', $this->cpf);
        }
    }

    public static function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'min:3'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'cpf'      => ['nullable', 'string', 'min:11', 'max:14'],
            'phone'    => ['nullable', 'string'],
            'avatar'   => ['nullable', 'string'],   // será procesado/guardado en otra capa
            'document' => ['nullable', 'array'],    // cada elemento se valida aparte al subir
        ];
    }
}