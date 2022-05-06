<?php

namespace GetCandy\Shipping\Tests\Unit\Actions\Carts;

use GetCandy\Shipping\Resolvers\PostcodeResolver;
use GetCandy\Shipping\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @group getcandy.shipping-methods
 */
class ShippingMethodResolverTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_postcode_shipping_methods_from_cart()
    {

    }
}
