<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
        $name = $this->faker->unique()->words(3, true);
        return [
            'category_id' => \App\Models\Category::factory(),
            'name' => ucwords($name),
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 100000, 5000000), // Rp 100,000 to Rp 5,000,000
            'stock' => $this->faker->numberBetween(0, 100),
            'weight' => $this->faker->numberBetween(100, 3000), // 100g to 3kg
            'is_active' => true,
        ];
    }
}
