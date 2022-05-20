<?php

namespace GetCandy\Shipping\Tests\Unit\Drivers\ShippingMethods;

use GetCandy\DataTypes\ShippingOption;
use GetCandy\Models\Currency;
use GetCandy\Models\TaxClass;
use GetCandy\Shipping\DataTransferObjects\ShippingOptionRequest;
use GetCandy\Shipping\Drivers\ShippingMethods\FreeShipping;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Models\ShippingMethod;
use GetCandy\Shipping\Models\ShippingZone;
use GetCandy\Shipping\Tests\TestCase;
use GetCandy\Shipping\Tests\TestUtils;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @group getcandy.shipping.drivers
 */
class FreeShippingTest extends TestCase
{
    use RefreshDatabase, TestUtils;

    /** @test */
    public function can_get_free_shipping()
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
            'driver' => 'free-shipping',
            'data' => [
                'minimum_spend' => [
                    "{$currency->code}" => 500,
                ],
            ],
        ]);

        $cart = $this->createCart($currency, 500);

        $driver = new FreeShipping();

        $request = new ShippingOptionRequest(
            cart: $cart,
            shippingMethod: $shippingMethod
        );

        $shippingOption = $driver->resolve($request);

        $this->assertInstanceOf(ShippingOption::class, $shippingOption);
    }

    /** @test */
    public function cant_get_free_shipping_if_minimum_isnt_met()
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
            'driver' => 'free-shipping',
            'data' => [
                'minimum_spend' => [
                    "{$currency->code}" => 500,
                ],
            ],
        ]);

        $cart = $this->createCart($currency, 50);

        $driver = new FreeShipping();

        $request = new ShippingOptionRequest(
            cart: $cart,
            shippingMethod: $shippingMethod
        );

        $shippingOption = $driver->resolve($request);

        $this->assertNull($shippingOption);
    }

    /** @test */
    public function cant_get_free_shipping_if_currency_isnt_met()
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
            'driver' => 'free-shipping',
            'data' => [
                'minimum_spend' => [
                    'FOO' => 500,
                ],
            ],
        ]);

        $cart = $this->createCart($currency, 10000);

        $driver = new FreeShipping();

        $request = new ShippingOptionRequest(
            cart: $cart,
            shippingMethod: $shippingMethod
        );

        $shippingOption = $driver->resolve($request);

        $this->assertNull($shippingOption);
    }
}
