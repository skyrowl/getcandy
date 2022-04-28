<?php

namespace GetCandy\Shipping\Models;

use GetCandy\Base\BaseModel;
use GetCandy\Shipping\Factories\ShippingZoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingMethod extends BaseModel
{
    use HasFactory;

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Return a new factory instance for the model.
     *
     * @return \GetCandy\Shipping\Factories\ShippingZoneFactory
     */
    protected static function newFactory(): ShippingZoneFactory
    {
        return ShippingZoneFactory::new();
    }

    /**
     * Return the shipping zone relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
    }
}
