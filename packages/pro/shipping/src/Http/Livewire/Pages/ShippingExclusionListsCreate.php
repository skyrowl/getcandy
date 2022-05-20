<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Shipping\Models\ShippingExclusionList;

class ShippingExclusionListsCreate extends AbstractShippingExclusionList
{
    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            'list.name' => 'required|unique:'.ShippingExclusionList::class.',name',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        $this->list = new ShippingExclusionList;
        $this->products = collect();
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('shipping::exclusion-lists.create')
            ->layout('shipping::layout', [
                'title' => 'Shipping Exclusion List',
            ]);
    }
}
