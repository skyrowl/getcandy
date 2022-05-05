<?php

namespace GetCandy\Shipping\Http\Livewire\Components\ShippingMethods;

use GetCandy\Hub\Http\Livewire\Traits\HasPrices;
use GetCandy\Models\Currency;
use GetCandy\Shipping\Traits\ExcludesProducts;
use Illuminate\Support\Collection;

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
     * Return mapped tiered pricing.
     *
     * @param  \Illuminate\Support\Collection  $prices
     * @return \Illuminate\Support\Collection
     */
    private function mapTieredPrices(Collection $prices)
    {
        $data = collect();

        foreach ($prices->groupBy(['tier', 'customer_group_id']) as $customerGroups) {
            $data = $data->concat(
                $customerGroups->map(function ($prices, $tier) {
                    $default = $prices->first(fn ($price) => $price->currency->default);

                    $prices = $prices->mapWithKeys(function ($price) {
                        return [
                            $price->currency->code => [
                                'id'                => $price->id,
                                'currency_id'       => $price->currency_id,
                                'customer_group_id' => $price->customer_group_id,
                                'price'             => $price->price->decimal,
                                'compare_price'     => $price->compare_price->decimal,
                            ],
                        ];
                    });

                    foreach ($this->currencies as $currency) {
                        if (empty($prices[$currency->code])) {
                            $prices[$currency->code] = [
                                'price'       => null,
                                'currency_id' => $currency->id,
                            ];
                        }
                    }

                    return [
                        'customer_group_id' => $default->customer_group_id ?: '*',
                        'tier'              => $default->tier / 100,
                        'prices'            => $prices,
                    ];
                })->values()
            );
        }

        return $data->sortBy('tier')->values();
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
                return ! $price['price'];
            })
        );

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
