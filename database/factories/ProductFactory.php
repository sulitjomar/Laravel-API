<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->company(),
            'price' => fake()->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 100),
            'available_stock' => fake()->numberBetween($min =1 , $max = 16),
            'is_for_sale' => fake()->boolean($chanceOfGettingTrue = 50)
        ];
    }
}
