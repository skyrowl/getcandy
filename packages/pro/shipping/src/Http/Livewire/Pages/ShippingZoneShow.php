<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Hub\Http\Livewire\Traits\Notifies;
use GetCandy\Models\Product;
use GetCandy\Shipping\Facades\Shipping;

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
     * Save the ShippingZone.
     *
     * @return void
     */
    public function save()
    {
        $this->shippingZone->save();

        if ($this->shippingZone->type != 'countries') {
            $this->shippingZone->countries()->detach();
            $this->selectedCountries = [];
        } else {
            $this->shippingZone->countries()->sync(
                $this->selectedCountries
            );
        }

        $this->notify('Shipping Zone updated');
    }

    /**
     * Return the available shipping methods.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getShippingMethodsProperty()
    {
        $methods = $this->shippingZone->shippingMethods;

        return Shipping::getSupportedDrivers()->map(function ($driver, $key) use ($methods) {
            $method = $methods->first(fn ($method) => $method->driver == $key);

            return [
                'name' => $driver->name(),
                'description' => $driver->description(),
                'component' => $driver->component(),
                'method' => $method,
                'enabled' => $method->enabled ?? false,
            ];
        });
    }

    /**
     * Toggle Shipping Method availability.
     */
    public function toggleMethod($key)
    {
        $map = $this->shippingMethods[$key];

        if ($map['method']) {
            $map['method']->update([
                'enabled' => ! $map['enabled'],
            ]);
            $map['method']->refresh();

            return;
        }

        $this->shippingZone->shippingMethods()->create([
            'name' => $map['name'],
            'description' => $map['description'],
            'enabled' => true,
            'driver' => $key,
        ]);

        $this->refresh();
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
