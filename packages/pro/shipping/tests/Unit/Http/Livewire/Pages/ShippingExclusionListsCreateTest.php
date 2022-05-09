<?php

namespace GetCandy\Shipping\Tests\Unit\Http\Livewire\Components\Pages;

use GetCandy\Hub\Models\Staff;
use GetCandy\Models\Product;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingExclusionListsCreate;
use GetCandy\Shipping\Models\ShippingExclusion;
use GetCandy\Shipping\Models\ShippingExclusionList;
use GetCandy\Shipping\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

/**
 * @group hub.shipping.lists
 */
class ShippingExclusionListsCreateTest extends TestCase
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
            ->test(ShippingExclusionListsCreate::class);
    }

    /** @test  */
    public function validation_is_applied_on_create()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        LiveWire::actingAs($staff, 'staff')
            ->test(ShippingExclusionListsCreate::class)
            ->call('save')
            ->assertHasErrors([
                'list.name' => 'required',
            ]);
    }

    /** @test  */
    public function can_create_exclusion_list()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $component = LiveWire::actingAs($staff, 'staff')
            ->test(ShippingExclusionListsCreate::class)
            ->set('list.name', 'Foo Bar')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas((new ShippingExclusionList())->getTable(), [
            'name' => $component->get('list.name'),
        ]);
    }

    /** @test  */
    public function can_create_exclusion_list_with_products()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $products = Product::factory(5)->create();

        $component = LiveWire::actingAs($staff, 'staff')
            ->test(ShippingExclusionListsCreate::class)
            ->set('list.name', 'Foo Bar')
            ->call('selectProducts', $products->pluck('id')->toArray())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas((new ShippingExclusionList())->getTable(), [
            'name' => $component->get('list.name'),
        ]);

        foreach ($products as $product) {
            $this->assertDatabaseHas((new ShippingExclusion())->getTable(), [
                'purchasable_id' => $product->id,
                'shipping_exclusion_list_id' => $component->get('list.id'),
            ]);
        }
    }
}
