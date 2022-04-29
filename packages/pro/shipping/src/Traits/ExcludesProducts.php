<?php

namespace GetCandy\Shipping\Traits;

use GetCandy\Models\Product;
use Illuminate\Support\Facades\DB;

trait ExcludesProducts
{
    /**
     * Return the trait listeners.
     *
     * @return array
     */
    public function excludesProductsListeners()
    {
        return [
            'product-search.selected' => 'selectProducts'
        ];
    }

    public function selectProducts($productIds)
    {
        DB::transaction(function () use ($productIds) {
            $this->shippingMethod->shippingExclusions()->createMany(
                collect($productIds)->map(function ($productId) {
                    return [
                        'purchasable_id' => $productId,
                        'purchasable_type' => Product::class,
                    ];
                })->toArray()
            );
        });
    }

    /**
     * Return the exclusions collection.
     *
     * @return void
     */
    public function getExclusionsProperty()
    {
        return $this->shippingMethod->shippingExclusions()->get();
    }
}
