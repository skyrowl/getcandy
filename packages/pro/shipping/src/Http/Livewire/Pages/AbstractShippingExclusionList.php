<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Hub\Http\Livewire\Traits\Notifies;
use GetCandy\Models\Product;
use GetCandy\Shipping\Models\ShippingExclusionList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

abstract class AbstractShippingExclusionList extends Component
{
    use Notifies;

    /**
     * The ShippingExclusionList instance.
     *
     * @var ShippingExclusionList
     */
    public ShippingExclusionList $list;

    /**
     * Array of products to associate to the list.
     *
     * @var \Illuminate\Support\Collection
     */
    public Collection $products;

    /**
     * {@inheritDoc}
     */
    protected $listeners = [
        'product-search.selected' => 'selectProducts',
    ];

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
     * Select products for the list.
     *
     * @param  array  $productIds
     * @return void
     */
    public function selectProducts($productIds)
    {
        $products = Product::findMany($productIds);

        $this->products = $this->products->merge(
            $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->translateAttribute('name'),
                    'thumbnail' => $product->thumbnail?->getUrl('small'),
                ];
            })
        );

        $this->emit('updatedExistingProductAssociations', $products);
    }

    /**
     * Remove a product from the array.
     *
     * @param  int  $index
     * @return void
     */
    public function removeProduct($index)
    {
        unset($this->products[$index]);
        $this->emit(
            'updatedExistingProductAssociations',
            Product::findMany(collect($this->products)->pluck('id'))
        );
    }

    /**
     * Save the exclusion list.
     *
     * @return void
     */
    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $this->list->save();
            $this->list->exclusions()->delete();
            $this->list->exclusions()->createMany(
                $this->products->map(function ($product) {
                    return [
                        'purchasable_type' => Product::class,
                        'purchasable_id' => $product['id'],
                    ];
                })
            );
        });

        $this->notify('Shipping Exclusion List Created');

        redirect()->route('hub.exclusion-lists.show', $this->list->id);

        // return redirect()->route(

        // );
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
