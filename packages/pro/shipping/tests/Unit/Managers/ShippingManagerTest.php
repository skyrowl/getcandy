<?php

namespace GetCandy\Shipping\Tests\Unit\Actions\Carts;

use GetCandy\Models\Country;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Models\ShippingZone;
use GetCandy\Shipping\Resolvers\ShippingZoneResolver;
use GetCandy\Shipping\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @group getcandy.shipping
 */
class ShippingManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function zones_method_uses_shipping_zone_resolver()
    {
        $resolver = Shipping::zones();
        $this->assertInstanceOf(ShippingZoneResolver::class, $resolver);
    }
}
