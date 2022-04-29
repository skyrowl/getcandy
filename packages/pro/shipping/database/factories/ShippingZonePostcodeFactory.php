<?php

namespace GetCandy\Shipping\Factories;

use GetCandy\Models\ShippingZone;
use GetCandy\Shipping\Models\ShippingZonePostcode;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingZonePostcodeFactory extends Factory
{
    protected $model = ShippingZonePostcode::class;

    public function definition(): array
    {
        return [
            'postcode' => $this->faker->postcode,
        ];
    }
}
