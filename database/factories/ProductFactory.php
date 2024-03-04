<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'api_parent_id' => $this->faker->numberBetween(-10000, 10000),
            'api_id' => $this->faker->numberBetween(-10000, 10000),
            'brand_id' => Brand::factory(),
            'supplier_id' => $this->faker->randomNumber(),
            'title' => $this->faker->sentence(4),
            'reference' => $this->faker->word(),
            'price' => $this->faker->randomFloat(3, 0, 99999.999),
            'sale_price' => $this->faker->randomFloat(3, 0, 99999.999),
            'rr_price' => $this->faker->randomFloat(3, 0, 99999.999),
            'stock' => $this->faker->numberBetween(-10000, 10000),
            'images' => '{}',
            'category_id' => Category::factory(),
        ];
    }
}
