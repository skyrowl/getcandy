<?php

namespace GetCandy\Shipping\Http\Livewire\Components\ShippingMethods;

use GetCandy\Hub\Http\Livewire\Traits\Notifies;
use GetCandy\Models\CustomerGroup;
use GetCandy\Shipping\Models\ShippingMethod;
use GetCandy\Shipping\Models\ShippingZone;
use Livewire\Component;

abstract class AbstractShippingMethod extends Component
{
    use Notifies;

    /**
     * The related ShippingZone.
     *
     * @var ShippingZone
     */
    public ShippingZone $shippingZone;

    /**
     * The ShippingMethod we're editing or creating.
     *
     * @var ShippingMethod
     */
    public ShippingMethod $shippingMethod;

    /**
     * The ID of the shipping method.
     *
     * @var int
     */
    public int $shippingMethodId;

    /**
     * Any additional rules for validation.
     *
     * @var array
     */
    public array $additionalRules = [];

    /**
     * Any additional data the shipping method needs.
     *
     * @var array
     */
    public array $data = [];

    /**
     * Define the base rules for this component.
     *
     * @return array
     */
    public function baseRules(): array
    {
        return [
            'data' => 'array',
            'shippingMethod.name' => 'string|nullable',
            'shippingMethod.description' => 'string|nullable',
            'shippingMethod.code' => 'required',
        ];
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
    public function rules()
    {
        return array_merge(
            $this->baseRules(),
            $this->additionalRules,
            $this->additionalRules()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        $this->shippingMethod = ShippingMethod::find(
            $this->shippingMethodId
        );

        $this->data = array_merge(
            $this->defaultData(),
            (array) $this->shippingMethod->data,
        );
    }

    /**
     * Return the default data required by the shipping method.
     *
     * @return array
     */
    abstract public function defaultData(): array;

    /**
     * Return the available customer groups.
     *
     * @return Collection
     */
    public function getCustomerGroupsProperty()
    {
        return CustomerGroup::get();
    }
}
