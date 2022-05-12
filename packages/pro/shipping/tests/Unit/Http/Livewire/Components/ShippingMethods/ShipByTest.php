<?php

namespace GetCandy\Shipping\Tests\Unit\Http\Livewire\Components\ShippingMethods;

use GetCandy\Models\Currency;
use GetCandy\Models\Price;
use GetCandy\Shipping\Models\ShippingZone;
use GetCandy\Shipping\Models\ShippingMethod;
use GetCandy\Shipping\Http\Livewire\Components\ShippingMethods\ShipBy;
use GetCandy\Shipping\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

/**
 * @group hub.shipping-methods.livewire
 */
class ShipByTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function can_update_shipping_method()
    {
        Currency::factory()->create([
            'default' => true,
        ]);

        $shippingZone = ShippingZone::factory()->create();

        $shippingMethod = ShippingMethod::factory()->create([
            'shipping_zone_id' => $shippingZone->id,
            'driver' => 'ship-by',
        ]);

        $component = Livewire::test(ShipBy::class, [
            'shippingMethodId' => $shippingMethod->id,
            'shippingZone' => $shippingZone,
        ])->assertSet('prices', [])
            ->set('shippingMethod.name', 'Foo bar')
            ->set('shippingMethod.description', 'Foo Description')
            ->set('shippingMethod.code', 'FOOSHIP')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            (new ShippingMethod)->getTable(),
            [
                'id' => $shippingMethod->id,
                'name' => 'Foo bar',
                'description' => 'Foo Description',
                'code' => 'FOOSHIP',
            ]
        );
    }

    /** @test */
    public function can_add_tier_prices_for_cart_total_type()
    {
        $currency = Currency::factory()->create([
            'default' => true,
        ]);

        $shippingZone = ShippingZone::factory()->create();

        $shippingMethod = ShippingMethod::factory()->create([
            'shipping_zone_id' => $shippingZone->id,
            'driver' => 'ship-by',
        ]);

        $component = Livewire::test(ShipBy::class, [
            'shippingMethodId' => $shippingMethod->id,
            'shippingZone' => $shippingZone,
        ])->assertSet('tieredPrices', collect())
            ->set('data.charge_by', 'cart_total')
            ->set('tieredPrices', collect([
                [
                    'tier' => '40',
                    'customer_group_id' => null,
                    'prices' => [
                        "{$currency->code}" => [
                            'price' => 50,
                            'currency_id' => $currency->id,
                        ]
                    ]
                ]
            ]))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            (new Price)->getTable(),
            [
                'priceable_type' => ShippingMethod::class,
                'priceable_id' => $shippingMethod->id,
                'currency_id' => $currency->id,
                'tier' => '4000',
                'price' => '5000',
            ]
        );
    }
}
