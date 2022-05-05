<?php

namespace GetCandy\Shipping\Tests\Unit\Actions\Carts;

use GetCandy\Models\Country;
use GetCandy\Shipping\DataTransferObjects\PostcodeLookup;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Models\ShippingZone;
use GetCandy\Shipping\Resolvers\ShippingZoneResolver;
use GetCandy\Shipping\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    /**
     * @test
     * @group moomoo
     */
    public function can_fetch_zone_by_postcode_lookup()
    {
        $country = Country::factory()->create();

        $shippingZone = ShippingZone::factory()->create([
            'type' => 'postcodes',
        ]);

        $shippingZone->countries()->attach($country);

        $shippingZone->postcodes()->create([
            'postcode' => 'ABC',
        ]);

        $this->assertCount(1, $shippingZone->refresh()->countries);
        $this->assertCount(1, $shippingZone->refresh()->postcodes);

        $postcode = new PostcodeLookup(
            $country,
            'ABC 123'
        );

        $zones = (new ShippingZoneResolver())->postcode($postcode)->get();

        $this->assertCount(1, $zones);

        $this->assertEquals($shippingZone->id, $zones->first()->id);
    }
}
