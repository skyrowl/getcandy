<?php

namespace GetCandy\Shipping\Tests\Unit\Drivers\ShippingMethods;

use GetCandy\DataTypes\ShippingOption;
use GetCandy\Models\Currency;
use GetCandy\Models\TaxClass;
use GetCandy\Shipping\DataTransferObjects\ShippingOptionRequest;
use GetCandy\Shipping\Drivers\ShippingMethods\ShipBy;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Models\ShippingMethod;
use GetCandy\Shipping\Models\ShippingZone;
use GetCandy\Shipping\Resolvers\ShippingZoneResolver;
use GetCandy\Shipping\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GetCandy\Shipping\Tests\TestUtils;

/**
 * @group getcandy.shipping.drivers
 */
class ShipByTest extends TestCase
{
    use RefreshDatabase, TestUtils;

    /** @test */
    public function can_get_shipping_option_by_cart_total()
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
            'driver' => 'ship-by',
            'data' => [
                'charge_by' => 'cart_total',
            ],
        ]);

        $shippingMethod->prices()->createMany([
            [
                'price' => 1000,
                'tier' => 1,
                'currency_id' => $currency->id,
            ],
            [
                'price' => 500,
                'tier' => 700,
                'currency_id' => $currency->id,
            ]
        ]);

        $this->assertCount(2, $shippingMethod->prices);

        $cart = $this->createCart($currency, 100);

        $driver = new ShipBy();

        $request = new ShippingOptionRequest(
            cart: $cart,
            shippingMethod: $shippingMethod
        );

        $shippingOption = $driver->resolve($request);

        $this->assertInstanceOf(ShippingOption::class, $shippingOption);

        $this->assertEquals(1000, $shippingOption->price->value);

        $cart = $this->createCart($currency, 10000);

        $driver = new ShipBy();

        $request = new ShippingOptionRequest(
            cart: $cart,
            shippingMethod: $shippingMethod
        );

        $shippingOption = $driver->resolve($request);

        $this->assertInstanceOf(ShippingOption::class, $shippingOption);

        $this->assertEquals(500, $shippingOption->price->value);
    }

    /** @test */
    public function can_get_shipping_option_if_outside_tier_without_default_price()
    {
        // Boom.
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
            'driver' => 'ship-by',
            'data' => [
                'charge_by' => 'cart_total',
            ],
        ]);

        $shippingMethod->prices()->createMany([
            [
                'price' => 500,
                'tier' => 700,
                'currency_id' => $currency->id,
            ]
        ]);

        $this->assertCount(1, $shippingMethod->prices);

        $cart = $this->createCart($currency, 100);

        $driver = new ShipBy();

        $request = new ShippingOptionRequest(
            cart: $cart,
            shippingMethod: $shippingMethod
        );

        $shippingOption = $driver->resolve($request);

        $this->assertNull($shippingOption);
    }
}
