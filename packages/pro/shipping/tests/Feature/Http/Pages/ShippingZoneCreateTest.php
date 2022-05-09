<?php

namespace GetCandy\Shipping\Tests\Feature\Http\Livewire\Pages;

use GetCandy\Hub\Models\Staff;
use GetCandy\Shipping\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @group hub.shipping.feature
 */
class ChannelShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cant_view_page_as_guest()
    {
        $this->get(route('hub.shipping.index'))
            ->assertRedirect(
                route('hub.login')
            );
    }

    /** @test */
    public function cant_view_page_without_permission()
    {
        $staff = Staff::factory()->create([
            'admin' => false,
        ]);

        $this->actingAs($staff, 'staff');

        $this->get(route('hub.shipping.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function can_view_page_as_admin()
    {
        $staff = Staff::factory()->create([
            'admin' => true,
        ]);

        $this->actingAs($staff, 'staff');

        $this->get(route('hub.shipping.index'))
            ->assertStatus(200);
    }

    /** @test */
    public function can_view_page_with_correct_permissions()
    {
        $staff = Staff::factory()->create([
            'admin' => false,
        ]);

        $this->actingAs($staff, 'staff');

        $staff->permissions()->createMany([
            [
                'handle' => 'shipping:manage',
            ],
        ]);

        $this->get(route('hub.shipping.index'))
            ->assertStatus(200);
    }
}
