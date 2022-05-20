<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Hub\Http\Livewire\Traits\Notifies;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Models\ShippingMethod;
use GetCandy\Shipping\Models\ShippingZone;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ShippingZoneShow extends AbstractShippingZone
{
    use Notifies;

    /**
     * The key of the shipping method to edit.
     *
     * @var string
     */
    public $methodToEdit = null;

    /**
     * The shipping methods enabled for this zone.
     *
     * @var array
     */
    public array $shippingMethods;

    /**
     * Whether to show the delete confirmation modal.
     *
     * @var bool
     */
    public bool $showDeleteConfirm = false;

    /**
     * The ID of the shipping method we want to remove.
     *
     * @var int
     */
    public ?int $shippingMethodToRemove = null;

    /**
     * {@inheritDoc}
     */
    protected $listeners = [
        'shippingMethodUpdated',
    ];

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return array_merge([
            'shippingZone.name' => 'required|unique:'.ShippingZone::class.',name,'.$this->shippingZone->id,
            'shippingZone.type' => 'required',
            // 'countries' => 'nullable|array',
        ], $this->baseRules());
    }

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        parent::mount();

        // If we don't have any shipping methods on the zone already, we should set the base ones up.
        $this->shippingMethods = $this->mapShippingMethods(
            $this->shippingZone->shippingMethods
        );
    }

    /**
     * Maps the shipping methods from the given zone.
     *
     * @param  Collection  $shippingMethods
     * @return Collection
     */
    public function mapShippingMethods(Collection $shippingMethods)
    {
        return $shippingMethods->mapWithKeys(function ($method, $key) {
            $driver = $this->supportedShippingMethods->first(function ($driver) use ($method) {
                return $driver['key'] == $method->driver;
            });

            if (! $driver) {
                return null;
            }

            return [
                "{$method->id}_{$driver['key']}" => [
                    'id' => $method->id,
                    'driver' => $driver['key'],
                    'name' => $driver['name'],
                    'description' => $driver['description'],
                    'custom_name' => $method->name,
                    'custom_description' => $method->description,
                    'component' => $driver['component'],
                    'method_id' => $method->id,
                    'enabled' => $method->enabled ?? false,
                ],
            ];
        })->filter()->toArray();
    }

    /**
     * Save the ShippingZone.
     *
     * @return void
     */
    public function save()
    {
        $this->shippingZone->save();

        $this->saveDetails();

        $this->notify('Shipping Zone updated');
    }

    /**
     * Deletes a shipping zone.
     *
     * @return Redirect
     */
    public function deleteZone()
    {
        DB::transaction(function () {
            $methods = $this->shippingZone->refresh()->shippingMethods;

            foreach ($methods as $method) {
                $method->prices()->delete();
                $method->shippingExclusions()->detach();
                $method->delete();
            }

            $this->shippingZone->postcodes()->delete();
            $this->shippingZone->countries()->detach();
            $this->shippingZone->states()->detach();

            $this->shippingZone->delete();
        });

        redirect()->route('hub.shipping.index');
    }

    /**
     * Delete the shipping method.
     *
     * @param  int  $shippingMethodId
     * @return void
     */
    public function deleteMethod()
    {
        $shippingMethod = ShippingMethod::find(
            $this->shippingMethodToRemove
        );

        $shippingMethod->prices()->delete();
        $shippingMethod->shippingExclusions()->detach();
        $shippingMethod->delete();

        $this->shippingMethodToRemove = null;

        $this->shippingMethods = $this->mapShippingMethods(
            $this->shippingZone->refresh()->shippingMethods
        );

        $this->notify('Shipping method deleted');
    }

    /**
     * Return the shipping methods supported by the system.
     *
     * @return Collection
     */
    public function getSupportedShippingMethodsProperty()
    {
        return Shipping::getSupportedDrivers()->map(function ($method, $key) {
            return [
                'key' => $key,
                'name' => $method->name(),
                'description' => $method->description(),
                'component' => $method->component(),
            ];
        });
    }

    public function addShippingMethod($key)
    {
        DB::transaction(function () use ($key) {
            $driver = $this->supportedShippingMethods[$key];

            $method = ShippingMethod::create([
                'shipping_zone_id' => $this->shippingZone->id,
                'name' => $driver['name'],
                'enabled' => true,
                'driver' => $key,
            ]);

            $this->shippingMethods["{$method->id}_{$key}"] = [
                'id' => $method->id,
                'name' => $driver['name'],
                'custom_name' => null,
                'custom_description' => null,
                'description' => $driver['description'],
                'component' => $driver['component'],
                'method_id' => $method->id,
                'enabled' => true,
            ];
        });

        $this->notify('Shipping method added');
    }

    /**
     * Toggle Shipping Method availability.
     */
    public function toggleMethod($key)
    {
        $map = $this->shippingMethods[$key];

        if ($map['method_id']) {
            ShippingMethod::whereId($map['method_id'])->update([
                'enabled' => ! $map['enabled'],
            ]);
        } else {
            $this->shippingZone->shippingMethods()->create([
                'name' => $map['name'],
                'description' => $map['description'],
                'enabled' => true,
                'driver' => $key,
            ]);
        }

        $this->shippingMethods[$key]['enabled'] = ! $map['enabled'];

        $this->notify(
            $map['enabled'] ?
            'Shipping method disabled' :
            'Shipping method enabled'
        );
    }

    /**
     * Handle the shipping method being saved.
     *
     * @return void
     */
    public function shippingMethodUpdated()
    {
        $this->methodToEdit = null;
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('shipping::shipping-zones.show')
            ->layout('shipping::layout', [
                'title' => $this->shippingZone->name,
            ]);
    }
}
