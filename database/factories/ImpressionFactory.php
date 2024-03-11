<?php

namespace Database\Factories;

use App\Models\Impression;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImpressionFactory extends Factory
{
    protected $model = Impression::class;

    public function definition()
    {
        return [
            'promotion_id' => $this->faker->numberBetween(1, 10),
            'product_id' => $this->faker->numberBetween(1, 100),
        ];
    }
}
