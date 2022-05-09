<?php

namespace GetCandy\Shipping\Tests\Unit\Drivers\ShippingMethods;

use GetCandy\DataTypes\ShippingOption;
use GetCandy\Models\Currency;
use GetCandy\Models\TaxClass;
use GetCandy\Shipping\DataTransferObjects\ShippingOptionRequest;
use GetCandy\Shipping\Drivers\ShippingMethods\Collection;
use GetCandy\Shipping\Models\ShippingMethod;
use GetCandy\Shipping\Models\ShippingZone;
use GetCandy\Shipping\Tests\TestCase;
use GetCandy\Shipping\Tests\TestUtils;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @group getcandy.shipping.drivers
 */
class CollectionTest extends TestCase
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
            'data' => [],
        ]);

        $cart = $this->createCart($currency, 500);

        $driver = new Collection();

        $request = new ShippingOptionRequest(
            cart: $cart,
            shippingMethod: $shippingMethod
        );

        $shippingOption = $driver->resolve($request);

        $this->assertInstanceOf(ShippingOption::class, $shippingOption);

        $this->assertEquals(0, $shippingOption->price->value);
    }
}
