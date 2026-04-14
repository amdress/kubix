<?php

namespace Database\Factories;

use App\Models\Solution;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SolutionFactory extends Factory
{
    protected $model = Solution::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        return [
            'name'            => ucfirst($name),
            'slug'            => Str::slug($name),
            'description'     => $this->faker->sentence(),
            'base_git_branch' => 'main',
            'is_active'       => true,
            'features'        => ['feature_1', 'feature_2', 'feature_3'],
            'config_schema'   => ['theme' => 'light', 'notifications' => true],
        ];
    }
}