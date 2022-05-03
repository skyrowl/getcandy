<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Hub\Http\Livewire\Traits\Notifies;
use GetCandy\Models\Product;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Models\ShippingMethod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShippingZoneShow extends AbstractShippingZone
{
    use Notifies;

    /**
     * The key of the shipping method to edit.
     *
     * @var string
     */
    public $methodToEdit = '1_free-shipping';

    /**
     * The shipping methods enabled for this zone.
     *
     * @var array
     */
    public array $shippingMethods;

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
        $existingMethods = $this->shippingZone->shippingMethods;

        // If we don't have any shipping methods on the zone already, we should set the base ones up.
        if (!$existingMethods->count()) {
            $this->shippingMethods = $this->supportedShippingMethods->mapWithKeys(function ($driver, $key) {
                $method = ShippingMethod::create([
                    'shipping_zone_id' => $this->shippingZone->id,
                    'name' => $driver->name(),
                    'enabled' => false,
                    'driver' => $key,
                ]);

                return [
                    "{$method->id}_{$key}" => [
                        'name' => $driver->name(),
                        'description' => $driver->description(),
                        'component' => $driver->component(),
                        'method_id' => $method->id,
                        'enabled' => false,
                    ]
                ];
            })->toArray();
        } else {
            $this->shippingMethods = $existingMethods->mapWithKeys(function ($method, $key) {

                $driver = $this->supportedShippingMethods->first(function ($driver) use ($method) {
                    return $driver['key'] == $method->driver;
                });

                if (!$driver) {
                    return null;
                }

                return [
                    "{$method->id}_{$driver['key']}" => [
                        'name' => $driver['name'],
                        'description' => $driver['description'],
                        'component' => $driver['component'],
                        'method_id' => $method->id,
                        'enabled' => $method->enabled ?? false,
                    ]
                ];
            })->filter()->toArray();
        }
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
     * Return the shipping methods supported by the system
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
                'name' => $driver['name'],
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

        $this->shippingMethods[$key]['enabled'] = !$map['enabled'];
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
        $products = Product::inRandomOrder()->take(4)->get();

        return view('shipping::shipping-zones.show')
        ->layout('adminhub::layouts.app', [
            'title' => 'United Kingdom',
        ]);
    }
}
