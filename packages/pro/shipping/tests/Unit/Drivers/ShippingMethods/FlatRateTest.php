<?php

namespace GetCandy\Shipping\Tests\Unit\Drivers\ShippingMethods;

use GetCandy\DataTypes\ShippingOption;
use GetCandy\Models\Currency;
use GetCandy\Models\TaxClass;
use GetCandy\Shipping\DataTransferObjects\ShippingOptionRequest;
use GetCandy\Shipping\Drivers\ShippingMethods\Collection;
use GetCandy\Shipping\Drivers\ShippingMethods\FlatRate;
use GetCandy\Shipping\Models\ShippingMethod;
use GetCandy\Shipping\Models\ShippingZone;
use GetCandy\Shipping\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GetCandy\Shipping\Tests\TestUtils;

/**
 * @group getcandy.shipping.drivers
 */
class FlatRateTest extends TestCase
{
    use RefreshDatabase, TestUtils;

    /** @test */
    public function can_get_flat_rate_shipping()
    {
        $currency = Currency::factory()->create([
            'default' => true,
        ]);

        TaxClass::factory()->create([
            'default' => true,
        ]);

        $shippingZone = ShippingZone::factory()->create([
            'type' => 'countries',
        ]);

        $shippingMethod = ShippingMethod::factory()->create([
            'shipping_zone_id' => $shippingZone->id,
            'driver' => 'flat-rate',
            'data' => [
                'minimum_spend' => [
                    "{$currency->code}" => 200
                ]
            ],
        ]);

        $shippingMethod->prices()->createMany([
            [
                'price' => 600,
                'tier' => 1,
                'currency_id' => $currency->id,
            ]
        ]);

        $cart = $this->createCart($currency, 500);

        $driver = new FlatRate();

        $request = new ShippingOptionRequest(
            cart: $cart,
            shippingMethod: $shippingMethod
        );

        $shippingOption = $driver->resolve($request);

        $this->assertInstanceOf(ShippingOption::class, $shippingOption);

        $this->assertEquals(600, $shippingOption->price->value);
    }
}
