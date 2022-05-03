<?php

namespace GetCandy\Shipping\Http\Livewire\Components\ShippingMethods;

use GetCandy\Hub\Http\Livewire\Traits\HasPrices;
use GetCandy\Models\Currency;
use GetCandy\Shipping\Traits\ExcludesProducts;

class ShipBy extends AbstractShippingMethod
{
    use ExcludesProducts, HasPrices;

    /**
     * The current currency.
     *
     * @var Currency
     */
    public Currency $currency;

    /**
     * The prices for the shipping method
     *
     * @var array
     */
    public array $prices = [];

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        parent::mount();

        $this->currency = $this->currencies->first(fn($currency) => $currency->default);

        // dd($this->shippingMethod->prices);
    }

    /**
     * {@inheritDoc}
     */
    public function defaultData(): array
    {
        return [
            'charge_by' => null,
        ];
    }

    /**
     * Return any additional rules for validation.
     *
     * @return array
     */
    public function additionalRules(): array
    {
        $currencies = $this->currencies;

        $rules = [];

        foreach ($currencies as $currency) {
            $rules["prices.*.{$currency->code}"] = 'numeric|required';
        }

        return $rules;
    }

    /**
     * {@inheritDoc}
     */
    public function save()
    {
        $this->validate();

        $this->shippingMethod->data = $this->data;

        $this->shippingMethod->save();

        $this->savePricing(
            basePrices: collect($this->basePrices)->reject(function ($price) {
                return !$price['price'];
            })
        );

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
     * {@inheritDoc}
     */
    public function getPricedModel()
    {
        return $this->shippingMethod;
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('shipping::shipping-methods.ship-by')
        ->layout('adminhub::layouts.app', [
            'title' => 'Flat Rate Shipping',
        ]);
    }
}
