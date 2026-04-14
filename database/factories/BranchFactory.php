<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->city();
        
        return [
            'name'           => $name,
            'code'           => strtoupper($this->faker->unique()->lexify('???')), // Ej: CWB, MAO
            'slug'           => Str::slug($name),
            'git_branch'     => 'main', // Por defecto todas nacen en main
            'is_active'      => true,
            'is_maintenance' => false,
            'branding'       => [
                'primary_color'   => $this->faker->hexColor(),
                'secondary_color' => $this->faker->hexColor(),
                'logo_path'       => 'branding/default_logo.png',
            ],
            'settings'       => [
                'timezone' => 'America/Sao_Paulo',
                'currency' => 'BRL',
                'locale'   => 'pt_BR',
            ],
        ];
    }

    /**
     * Estado para cuando una sucursal está en mantenimiento.
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_maintenance' => true,
        ]);
    }
}