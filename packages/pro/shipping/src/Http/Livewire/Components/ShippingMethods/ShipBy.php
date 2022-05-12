<?php

namespace GetCandy\Shipping\Http\Livewire\Components\ShippingMethods;

use GetCandy\Hub\Http\Livewire\Traits\HasPrices;
use GetCandy\Models\Currency;
use GetCandy\Shipping\Traits\ExcludesProducts;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShipBy extends AbstractShippingMethod
{
    use ExcludesProducts;

    /**
     * The current currency.
     *
     * @var Currency
     */
    public Currency $currency;

    /**
     * The tiers for the shipping method.
     *
     * @var array
     */
    public array $tiers = [];

    /**
     * The prices for the shipping method.
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
        $this->currency = $this->currencies->first(fn ($currency) => $currency->default);
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
        // dd($this->tieredPrices);
        $this->validate();

        $this->shippingMethod->data = $this->data;

        $this->shippingMethod->save();

        DB::transaction(function () {
            foreach ($this->tiers as $data) {
                foreach ($data['prices'] as $currencyCode => $price) {
                    if ($this->data['charge_by'] == 'cart_total') {
                        $price['value'] = $price['value'] * 100;
                    }
                    if ($price['id'] ?? null) {
                        $this->shippingMethod->prices()->where('id', '=', $price['id'])->update([
                            'price' => $price['value'],
                            'customer_group_id' => $data['customer_group_id'],
                        ]);
                        continue;
                    }

                    $currency = Currency::whereCode($currencyCode)->first();

                    $this->shippingMethod->prices()->create([
                        'tier' => $data['tier'],
                        'price' => $price['value'],
                        'currency_id' => $currency->id,
                        'customer_group_id' => $data['customer_group_id'],
                    ]);
                }
            }
        });

        // $this->savePricing(
        //     basePrices: collect($this->basePrices)->reject(function ($price) {
        //         return ! $price['price'];
        //     })
        // );

        $this->updateExcludedLists();

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
