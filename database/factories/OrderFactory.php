<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'user_id' => User::factory(),
            'total_price' => $this->faker->randomFloat(2, 200000, 5000000),
            'status' => $this->faker->randomElement(['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled']),
            'shipping_address' => $this->faker->address(),
            'shipping_cost' => $this->faker->randomElement([0, 15000, 25000, 45000]),
            'notes' => $this->faker->sentence(),
        ];
    }
}
