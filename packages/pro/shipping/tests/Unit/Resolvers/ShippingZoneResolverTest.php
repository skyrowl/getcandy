<?php

namespace GetCandy\Shipping\Tests\Unit\Actions\Carts;

use GetCandy\Models\Country;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Models\ShippingZone;
use GetCandy\Shipping\Resolvers\ShippingZoneResolver;
use GetCandy\Shipping\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;

/**
 * @group getcandy.shipping
 */
class ShippingZoneResolverTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_shipping_zones_by_country()
    {
        $countryA = Country::factory()->create();
        $countryB = Country::factory()->create();

        $shippingZoneA = ShippingZone::factory()->create([
            'type' => 'countries',
        ]);

        $shippingZoneB = ShippingZone::factory()->create([
            'type' => 'countries',
        ]);

        $shippingZoneA->countries()->attach($countryA);
        $shippingZoneB->countries()->attach($countryB);

        $this->assertCount(1, $shippingZoneA->refresh()->countries);

        $zones = (new ShippingZoneResolver())->country($countryA)->get();

        $this->assertCount(1, $zones);

        $this->assertEquals($shippingZoneA->id, $zones->first()->id);
    }

    /** @test */
    public function doesnt_fetch_postcode_shipping_zones_by_country()
    {
        $countryA = Country::factory()->create();

        $shippingZoneA = ShippingZone::factory()->create([
            'type' => 'postcodes',
        ]);

        $shippingZoneA->countries()->attach($countryA);

        $this->assertCount(1, $shippingZoneA->refresh()->countries);

        $zones = (new ShippingZoneResolver())->country($countryA)->get();

        $this->assertEmpty($zones);
    }

    /** @test */
    public function cant_fetch_zone_by_postcode_without_country()
    {
        $country = Country::factory()->create();

        $shippingZoneA = ShippingZone::factory()->create([
            'type' => 'postcodes',
        ]);

        $shippingZoneA->countries()->attach($country);

        $this->assertCount(1, $shippingZoneA->refresh()->countries);

        $this->expectException(InvalidArgumentException::class);

        $zones = (new ShippingZoneResolver())->postcode('ABC 123')->get();
    }
}
