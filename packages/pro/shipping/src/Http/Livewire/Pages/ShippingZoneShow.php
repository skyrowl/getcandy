<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Hub\Http\Livewire\Traits\Notifies;
use GetCandy\Models\CustomerGroup;
use GetCandy\Models\Product;
use Livewire\Component;

class ShippingZoneShow extends AbstractShippingZone
{
    use Notifies;

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

    public function save()
    {
        $this->shippingZone->save();
        $this->notify('Shipping Zone updated');
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $products = Product::inRandomOrder()->take(4)->get();

        return view('shipping::shipping-zones.show', [
            'products' => $products,
        ])->layout('adminhub::layouts.app', [
            'title' => 'United Kingdom',
        ]);
    }
}
