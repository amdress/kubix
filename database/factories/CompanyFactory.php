<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $companyName = $this->faker->unique()->company();
        
        return [
            'cnpj'          => $this->faker->unique()->numerify('##.###.###/0001-##'),
            'company_name'  => $companyName,
            'trade_name'    => $companyName . ' ' . $this->faker->companySuffix(),
            'slug'          => Str::slug($companyName),
            'billing_email' => $this->faker->unique()->companyEmail(),
            'phone'         => $this->faker->phoneNumber(),
            'branding'      => [
                'logo_url'      => 'logos/default-company.png',
                'primary_color' => $this->faker->hexColor(),
            ],
            'status'        => 'active',
            'is_active'     => true,
        ];
    }
}