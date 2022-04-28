<?php

namespace GetCandy\Shipping\Http\Livewire\Components\ShippingMethods;

use Livewire\Component;

class FreeShipping extends AbstractShippingMethod
{
    /**
     * {@inheritDoc}
     */
    public function defaultData(): array
    {
        return [
            'minimum_spend' => null,
            'use_discount_amount' => false,
        ];
    }

    /**
     * Return any additional rules for validation
     *
     * @return array
     */
    public function additionalRules(): array
    {
        return [
            'data.minimum_spend' => 'numeric|nullable',
            'data.use_discount_amount' => 'boolean|nullable',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->shippingMethod->data = $this->data;
        $this->shippingMethod->save();

        $this->notify('Shipping Method Updated');

        $this->emit('shippingMethodUpdated');
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('shipping::shipping-methods.free-shipping')
        ->layout('adminhub::layouts.app', [
            'title' => 'United Kingdom',
        ]);
    }
}
