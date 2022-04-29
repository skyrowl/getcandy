<?php

namespace GetCandy\Shipping\Http\Livewire\Components\ShippingMethods;

use GetCandy\Models\Currency;
use GetCandy\Shipping\Traits\ExcludesProducts;

class FreeShipping extends AbstractShippingMethod
{
    use ExcludesProducts;

    /**
     * The current currency.
     *
     * @var Currency
     */
    public Currency $currency;

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        parent::mount();

        $this->currency = $this->currencies->first();
    }

    /**
     * {@inheritDoc}
     */
    public function defaultData(): array
    {
        return [
            'minimum_spend' => [],
            'use_discount_amount' => false,
        ];
    }

    /**
     * Return any additional rules for validation.
     *
     * @return array
     */
    public function additionalRules(): array
    {
        return [
            'data.minimum_spend' => 'array|nullable',
            'data.use_discount_amount' => 'boolean|nullable',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function save()
    {
        $this->validate();

        $this->shippingMethod->data = $this->data;

        $this->updateExcludedLists();

        $this->shippingMethod->save();

        $this->notify('Shipping Method Updated');

        $this->emit('shippingMethodUpdated');
    }

    /**
     * Set the currency.
     *
     * @param  int  $currencyId
     * @return void
     */
    public function setCurrency($currencyId)
    {
        $this->currency = $this->currencies->first(fn ($currency) => $currency->id == $currencyId);
    }

    /**
     * Return the available currencies.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurrenciesProperty()
    {
        return Currency::get();
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
