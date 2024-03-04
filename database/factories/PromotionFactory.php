<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Campaign;
use App\Models\Promotion;

class PromotionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Promotion::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'category_id' => $this->faker->randomNumber(),
            'cost_per_click' => $this->faker->randomFloat(2, 0, 999.99),
            'budget' => $this->faker->randomFloat(2, 0, 999999.99),
        ];
    }
}
