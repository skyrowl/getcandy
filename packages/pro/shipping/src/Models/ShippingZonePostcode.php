<?php

namespace GetCandy\Shipping\Models;

use GetCandy\Base\BaseModel;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Factories\ShippingZonePostcodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingZonePostcode extends BaseModel
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
     * @return \GetCandy\Shipping\Factories\ShippingZonePostcodeFactory
     */
    protected static function newFactory(): ShippingZonePostcodeFactory
    {
        return ShippingZonePostcodeFactory::new();
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
     * Setter for postcode attribute
     *
     * @param string $value
     *
     * @return void
     */
    public function setPostcodeAttribute($value)
    {
        $this->attributes['postcode'] = str_replace(' ', '', $value);
    }
}
