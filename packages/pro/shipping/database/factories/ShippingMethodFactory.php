<?php

namespace GetCandy\Shipping\Database\Factories;

use GetCandy\Shipping\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingMethodFactory extends Factory
{
    protected $model = ShippingMethod::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence,
            'driver' => 'ship-by',
            'code' => $this->faker->word,
            'enabled' => true,
            'data' => [],
        ];
    }
}
