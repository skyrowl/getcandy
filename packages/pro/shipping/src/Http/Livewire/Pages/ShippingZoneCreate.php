<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Hub\Http\Livewire\Traits\Notifies;
use GetCandy\Models\Product;
use GetCandy\Shipping\Models\ShippingZone;

class ShippingZoneCreate extends AbstractShippingZone
{
    use Notifies;

    /**
     * The shipping zone instance.
     *
     * @var \GetCandy\Shipping\Models\ShippingZone
     */
    public ShippingZone $shippingZone;

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return array_merge([
            'shippingZone.name' => 'required|unique:'.ShippingZone::class.',name',
            'shippingZone.type' => 'required',
            // 'countries' => 'nullable|array',
        ], $this->baseRules());
    }

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        $this->shippingZone = new ShippingZone([
            'type' => 'unrestricted',
        ]);
    }

    public function save()
    {
        $this->validate();
        $this->shippingZone->save();
        $this->saveDetails();
        $this->emit('Shipping Zone Created');

        return redirect()->route('hub.shipping.shipping-zone.show', $this->shippingZone->id);
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $products = Product::inRandomOrder()->take(4)->get();

        return view('shipping::shipping-zones.create', [
            'products' => $products,
        ])->layout('adminhub::layouts.app', [
            'title' => __('shipping::create.title'),
        ]);
    }
}
