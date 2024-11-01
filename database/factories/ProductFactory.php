<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->domainName(),
            'sku' => $this->faker->ean8(),
            'price' => $this->faker->numberBetween(10_000, 1_000_000),
            'created_by' => 1,
            'updated_by' => 1
        ];
    }
}
