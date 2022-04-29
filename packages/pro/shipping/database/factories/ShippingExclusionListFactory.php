<?php

namespace GetCandy\Shipping\Factories;

use GetCandy\Models\Product;
use GetCandy\Shipping\Models\ShippingExclusionList;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingExclusionListFactory extends Factory
{
    protected $model = ShippingExclusionList::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
