<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'api_id' => $this->faker->randomNumber(),
            'name' => $this->faker->name(),
            'url' => $this->faker->url(),
            'level' => $this->faker->randomNumber(),
            'parent_id' => $this->faker->randomNumber(),
            'cost_per_click' => $this->faker->randomFloat(2, 0, 999.99),
        ];
    }
}
