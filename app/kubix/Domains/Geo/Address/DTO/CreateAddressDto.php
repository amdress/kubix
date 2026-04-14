<?php

namespace App\Kubix\Common\Address\DTO;

use Spatie\LaravelData\Data;

/**
 * CreateAddressDto
 *
 * DTO de entrada para cualquier flujo que necesite crear una dirección.
 * Solo valida y normaliza los campos que envía el frontend.
 */
class CreateAddressDto extends Data
{
    public function __construct(
        public ?string $zip_code = null,
        public ?string $street = null,
        public ?string $number = null,
        public ?string $complement = null,
        public ?string $neighborhood = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $country = null,
        public bool $is_primary = true,
        public ?string $reference = null,
    ) {}

    public static function rules(): array
    {
        return [
            'zip_code'     => ['nullable', 'string', 'max:20'],
            'street'       => ['nullable', 'string', 'max:150'],
            'number'       => ['nullable', 'string', 'max:20'],
            'complement'   => ['nullable', 'string', 'max:150'],
            'neighborhood' => ['nullable', 'string', 'max:100'],
            'city'         => ['nullable', 'string', 'max:100'],
            'state'        => ['nullable', 'string', 'max:100'],
            'country'      => ['nullable', 'string', 'max:3'], // ISO Alpha-3 opcional
            'type'         => ['required', 'in:point,area'],
            'is_primary'   => ['boolean'],
            'reference'    => ['nullable', 'string'],
        ];
    }
}