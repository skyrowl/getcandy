<?php

namespace GetCandy\Shipping\Models;

use GetCandy\Base\BaseModel;
use GetCandy\Base\Purchasable;
use GetCandy\Base\Traits\HasPrices;
use GetCandy\Models\TaxClass;
use GetCandy\Shipping\Database\Factories\ShippingMethodFactory;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;

class ShippingMethod extends BaseModel implements Purchasable
{
    use HasFactory, HasPrices;

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = [
        'data' => 'object',
    ];

    /**
     * Return a new factory instance for the model.
     *
     * @return \GetCandy\Shipping\Factories\ShippingMethodFactory
     */
    protected static function newFactory(): ShippingMethodFactory
    {
        return ShippingMethodFactory::new();
    }

    /**
     * Return the shipping zone relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
    }

    /**
     * Return the shipping method driver.
     *
     * @return \GetCandy\Shipping\Interfaces\ShippingMethodInterface
     */
    public function driver(): ShippingMethodInterface
    {
        return Shipping::driver($this->driver)->on($this);
    }

    /**
     * Return the shipping exclusions property.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingExclusions()
    {
        return $this->belongsToMany(
            ShippingExclusionList::class,
            config('getcandy.database.table_prefix').'exclusion_list_shipping_method',
            'method_id',
            'exclusion_id',
            // 'method_id',
        )->withTimestamps();
    }

    public function getPrices(): Collection
    {
        return $this->prices;
    }

    /**
     * Return the unit quantity for the variant.
     *
     * @return int
     */
    public function getUnitQuantity(): int
    {
        return 1;
    }

    /**
     * Return the tax class.
     *
     * @return \GetCandy\Models\TaxClass
     */
    public function getTaxClass(): TaxClass
    {
        return TaxClass::getDefault();
    }

    public function getTaxReference()
    {
        return $this->code;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'shipping';
    }

    /**
     * {@inheritDoc}
     */
    public function isShippable()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritDoc}
     */
    public function getOption()
    {
        return $this->code;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return collect();
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier()
    {
        return $this->code;
    }

    public function getThumbnail()
    {
        return null;
    }
}
