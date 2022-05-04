<?php

namespace GetCandy\Shipping\Http\Livewire\Components\ShippingMethods;

use GetCandy\Shipping\Traits\ExcludesProducts;

class Collection extends AbstractShippingMethod
{
    use ExcludesProducts;

    /**
     * {@inheritDoc}
     */
    public function defaultData(): array
    {
        return [];
    }

    /**
     * Return any additional rules for validation.
     *
     * @return array
     */
    public function additionalRules(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function save()
    {
        $this->validate();

        $this->shippingMethod->data = $this->data;

        // $this->updateExcludedLists();

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
        return view('shipping::shipping-methods.collection')
        ->layout('adminhub::layouts.app', [
            'title' => 'United Kingdom',
        ]);
    }
}
