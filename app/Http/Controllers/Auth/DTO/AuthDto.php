<?php

namespace App\Http\Controllers\Auth\DTO;

use Spatie\LaravelData\Data;

class AuthDto extends Data
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $device_name = null,
    ) {}

    /**
     * Regras de validação
     */
    public static function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'device_name' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Mensagens de erro claras e humanas
     */
    public static function messages(): array
    {
        return [
            'email.required'  => 'O e-mail é obrigatório.',
            'email.email'     => 'O e-mail informado não possui um formato válido.',
            'email.max'       => 'O e-mail não pode ultrapassar 255 caracteres.',

            'password.required' => 'A senha é obrigatória.',
            'password.min'      => 'A senha deve conter pelo menos 8 caracteres.',

            'device_name.string' => 'O nome do dispositivo deve ser um texto válido.',
            'device_name.max'    => 'O nome do dispositivo não pode ultrapassar 255 caracteres.',
        ];
    }
}