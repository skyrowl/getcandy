<?php

namespace GetCandy\Shipping\Tests\Unit\Http\Livewire\Components\Pages;

use GetCandy\Hub\Models\Staff;
use GetCandy\Models\Country;
use GetCandy\Models\State;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingZoneCreate;
use GetCandy\Shipping\Models\ShippingZone;
use GetCandy\Shipping\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

/**
 * @group hub.shipping.livewire
 */
class ProductCreateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test  */
    public function component_mounts_correctly()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        LiveWire::actingAs($staff, 'staff')
            ->test(ShippingZoneCreate::class);
    }

    /** @test  */
    public function validation_is_applied_on_create()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        LiveWire::actingAs($staff, 'staff')
            ->test(ShippingZoneCreate::class)
            ->call('save')
            ->assertHasErrors([
                'shippingZone.name' => 'required',
            ]);
    }

    /** @test  */
    public function can_create_shipping_zone()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $component = LiveWire::actingAs($staff, 'staff')
            ->test(ShippingZoneCreate::class)
            ->set('shippingZone.name', 'Foo bar')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            (new ShippingZone())->getTable(),
            [
                'name' => $component->get('shippingZone.name'),
                'type' => 'unrestricted',
            ]
        );
    }

    /** @test  */
    public function cannot_create_countries_shipping_zone_without_selecting_countries()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $component = LiveWire::actingAs($staff, 'staff')
            ->test(ShippingZoneCreate::class)
            ->set('shippingZone.name', 'Foo bar')
            ->set('shippingZone.type', 'countries')
            ->set('selectedCountries', [])
            ->call('save')
            ->assertHasErrors([
                'selectedCountries' => 'required_if',
            ]);
    }

    /** @test  */
    public function can_create_countries_shipping_zone()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $countries = Country::factory(5)->create();

        $component = LiveWire::actingAs($staff, 'staff')
            ->test(ShippingZoneCreate::class)
            ->set('shippingZone.name', 'Foo bar')
            ->set('shippingZone.type', 'countries')
            ->set('selectedCountries', $countries->pluck('id')->toArray())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            (new ShippingZone())->getTable(),
            [
                'name' => $component->get('shippingZone.name'),
                'type' => 'countries',
            ]
        );

        $shippingZone = $component->get('shippingZone');

        $this->assertCount($countries->count(), $shippingZone->countries);
    }

    /** @test  */
    public function cannot_create_states_shipping_zone_without_country_or_states()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $component = LiveWire::actingAs($staff, 'staff')
            ->test(ShippingZoneCreate::class)
            ->set('shippingZone.name', 'Foo bar')
            ->set('shippingZone.type', 'states')
            ->set('country', null)
            ->set('selectedStates', [])
            ->call('save')
            ->assertHasErrors([
                'selectedStates' => 'required_if',
            ]);
    }

    /** @test  */
    public function can_create_states_shipping_zone()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $country = Country::factory()->has(
            State::factory(5)
        )->create();

        $component = LiveWire::actingAs($staff, 'staff')
            ->test(ShippingZoneCreate::class)
            ->set('shippingZone.name', 'Foo bar')
            ->set('shippingZone.type', 'states')
            ->set('country', $country->id)
            ->set('selectedStates', $country->states->pluck('id')->toArray())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            (new ShippingZone())->getTable(),
            [
                'name' => $component->get('shippingZone.name'),
                'type' => 'states',
            ]
        );

        $shippingZone = $component->get('shippingZone');

        $this->assertCount(1, $shippingZone->countries);
        $this->assertEquals($country->id, $shippingZone->countries->first()->id);
        $this->assertCount($country->states->count(), $shippingZone->states);
    }

    /** @test  */
    public function cannot_create_postcodes_shipping_zone_without_country_or_postcodes()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $component = LiveWire::actingAs($staff, 'staff')
            ->test(ShippingZoneCreate::class)
            ->set('shippingZone.name', 'Foo bar')
            ->set('shippingZone.type', 'postcodes')
            ->set('country', null)
            ->set('postcodes', null)
            ->call('save')
            ->assertHasErrors([
                'postcodes' => 'required_if',
            ]);
    }

    /** @test */
    public function can_create_postcodes_shipping_zone()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $country = Country::factory()->create();

        $component = LiveWire::actingAs($staff, 'staff')
            ->test(ShippingZoneCreate::class)
            ->set('shippingZone.name', 'Foo bar')
            ->set('shippingZone.type', 'postcodes')
            ->set('country', $country->id)
            ->set('postcodes', "
                AB1 1TX \n
                AB2 2TX \n
                AB3 3TX
            ")
            ->call('save')
            ->assertHasNoErrors();

        $this->assertCount(3, $component->get('shippingZone')->postcodes);
    }
}
