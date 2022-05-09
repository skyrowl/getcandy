<?php

namespace GetCandy\Shipping\Tests\Unit\Actions\Carts;

use GetCandy\Models\CartAddress;
use GetCandy\Models\Country;
use GetCandy\Models\Currency;
use GetCandy\Models\TaxClass;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Models\ShippingMethod;
use GetCandy\Shipping\Models\ShippingZone;
use GetCandy\Shipping\Resolvers\ShippingZoneResolver;
use GetCandy\Shipping\Tests\TestCase;
use GetCandy\Shipping\Tests\TestUtils;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @group getcandy.shipping.manager
 */
class ShippingManagerTest extends TestCase
{
    use RefreshDatabase, TestUtils;

    /** @test */
    public function zones_method_uses_shipping_zone_resolver()
    {
        $resolver = Shipping::zones();
        $this->assertInstanceOf(ShippingZoneResolver::class, $resolver);
    }

    /** @test */
    public function can_fetch_expected_shipping_methods()
    {
        $currency = Currency::factory()->create([
            'default' => true,
        ]);

        $country = Country::factory()->create();

        TaxClass::factory()->create([
            'default' => true,
        ]);

        $shippingZone = ShippingZone::factory()->create([
            'type' => 'countries',
        ]);

        $shippingZone->countries()->attach($country);

        $shippingMethod = ShippingMethod::factory()->create([
            'shipping_zone_id' => $shippingZone->id,
            'driver' => 'ship-by',
            'data' => [
                'minimum_spend' => [
                    "{$currency->code}" => 200,
                ],
            ],
        ]);

        $shippingMethod->prices()->createMany([
            [
                'price' => 600,
                'tier' => 1,
                'currency_id' => $currency->id,
            ],
            [
                'price' => 500,
                'tier' => 700,
                'currency_id' => $currency->id,
            ],
            [
                'price' => 0,
                'tier' => 800,
                'currency_id' => $currency->id,
            ],
        ]);

        $cart = $this->createCart($currency, 500);

        $cart->shippingAddress()->create(
            CartAddress::factory()->make([
                'country_id' => $country->id,
                'state' => null,
            ])->toArray()
        );

        $shippingMethods = Shipping::shippingMethods(
            $cart->refresh()->getManager()->getCart()
        )->get();

        $this->assertCount(1, $shippingMethods);
        // $this->assertEquals()
    }
}
