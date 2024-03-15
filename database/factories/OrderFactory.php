<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
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
            'promotion_id' => $this->faker->numberBetween(1, 100),
            'products' => json_encode($this->faker->randomElements(range(1, 100), $this->faker->numberBetween(1, 10))),
            'amount_paid' => $this->faker->randomFloat(2, 10, 500),
            'country' => $this->faker->randomElement('GB'),
            'third_party_order_reference' => $this->faker->uuid(),
        ];
    }
}
