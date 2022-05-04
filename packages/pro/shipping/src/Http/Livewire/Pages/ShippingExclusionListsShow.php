<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Shipping\Models\ShippingExclusionList;

class ShippingExclusionListsShow extends AbstractShippingExclusionList
{
    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            'list.name' => 'required|unique:'.ShippingExclusionList::class.',name,'.$this->list->id,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        $this->products = $this->list
            ->load(['exclusions.purchasable.thumbnail', 'exclusions.purchasable.variants'])
        ->exclusions->pluck('purchasable')->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->translateAttribute('name'),
                'thumbnail' => $product->thumbnail?->getUrl('small'),
                'sku' => $product->variants->pluck('sku')->join(', '),
            ];
        })->sortBy('id');
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('shipping::exclusion-lists.show')
            ->layout('shipping::layout', [
                'title' => 'Shipping Exclusion List',
            ]);
    }
}
