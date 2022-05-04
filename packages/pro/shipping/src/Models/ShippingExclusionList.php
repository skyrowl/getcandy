<?php

namespace GetCandy\Shipping\Models;

use GetCandy\Base\BaseModel;
use GetCandy\Shipping\Factories\ShippingExclusionListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingExclusionList extends BaseModel
{
    use HasFactory;

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = [];

    /**
     * Return a new factory instance for the model.
     *
     * @return \GetCandy\Shipping\Factories\ShippingExclusionListFactory
     */
    protected static function newFactory(): ShippingExclusionListFactory
    {
        return ShippingExclusionListFactory::new();
    }

    /**
     * Return the shipping zone relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exclusions()
    {
        return $this->hasMany(ShippingExclusion::class);
    }

    /**
     * Return the shipping methods relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingMethods()
    {
        return $this->belongsToMany(
            ShippingMethod::class,
            config('getcandy.database.table_prefix') . 'exclusion_list_shipping_method',
            'exclusion_id',
            'method_id',
        );
    }
}
