<?php

namespace GetCandy\Shipping\Factories;

use GetCandy\Models\Product;
use GetCandy\Shipping\Models\ShippingExclusion;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingExclusionFactory extends Factory
{
    protected $model = ShippingExclusion::class;

    public function definition(): array
    {
        return [
            'purchasable_id' => 1,
            'purchasable_type' => Product::class,
        ];
    }
}
