<?php

namespace GetCandy\Shipping\Models;

use GetCandy\Base\BaseModel;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Factories\ShippingExclusionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingExclusion extends BaseModel
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
     * @return \GetCandy\Shipping\Factories\ShippingExclusionFactory
     */
    protected static function newFactory(): ShippingExclusionFactory
    {
        return ShippingExclusionFactory::new();
    }

    /**
     * Return the shipping zone relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function list()
    {
        return $this->belongsTo(ShippingZone::class);
    }

    /**
     * Return the purchasable relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function purchasable()
    {
        return $this->morphTo('purchasable');
    }
}
