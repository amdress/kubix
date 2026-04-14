<?php

namespace App\Kubix\Domains\Geo\Territory\DTO;

use Spatie\LaravelData\Data;

/**
 * CheckAvailabilityDto
 * Soporta entrada dual: Coordenadas GPS o Path de Territorio.
 */
class CheckAvailabilityDto extends Data
{
    public function __construct(
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $path = null,
    ) {}

    public static function rules(): array
    {
        return [
            // Latitude es obligatoria si NO hay path
            'latitude'  => ['required_without:path', 'nullable', 'numeric', 'between:-90,90'],
            
            // Longitude es obligatoria si NO hay path
            'longitude' => ['required_without:path', 'nullable', 'numeric', 'between:-180,180'],
            
            // Path es obligatorio si NO hay coordenadas
            'path'      => ['required_without:latitude', 'nullable', 'string', 'regex:/^\/([1-9][0-9]*\/)+$/'],
        ];
    }

    public static function messages(): array
    {
        return [
            'latitude.required_without'  => 'Informe a latitude ou o path do território.',
            'longitude.required_without' => 'Informe a longitude ou o path do território.',
            'path.required_without'      => 'Informe o path ou as coordenadas de localização.',
            'path.regex'                 => 'O formato do path é inválido (ex: /1/1/1/).',
            'latitude.numeric'           => 'A latitude deve ser um número decimal válido.',
            'longitude.numeric'          => 'A longitude deve ser um número decimal válido.',
        ];
    }
}