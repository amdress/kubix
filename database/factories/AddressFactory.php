<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            // Los polimórficos se dejan vacíos aquí para definirlos en el Seeder
            'addressable_id'   => null,
            'addressable_type' => null,
            
            // Campos según tu modelo
            'zip_code'     => fake()->numerify('########'), // 8 dígitos (sin guión para guardarlo crudo)
            'street'       => fake()->streetName(),
            'number'       => fake()->buildingNumber(),
            'complement'   => fake()->optional()->secondaryAddress(),
            'neighborhood' => fake()->word(),
            'city'         => fake()->city(),
            'state'        => strtoupper(fake()->lexify('??')), // Ej: PR, AM, SP
            'country'      => 'BR',
            
            // Coordenadas con la precisión de tu cast decimal:8
            'latitude'     => fake()->latitude(),
            'longitude'    => fake()->longitude(),
            
            'is_primary'   => true,
        ];
    }
}