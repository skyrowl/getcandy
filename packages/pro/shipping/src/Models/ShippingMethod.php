<?php

namespace GetCandy\Shipping\Models;

use GetCandy\Base\BaseModel;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Factories\ShippingZoneFactory;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;
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

    protected $casts = [
        'data' => 'object',
    ];

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

    /**
     * Return the shipping method driver.
     *
     * @return \GetCandy\Shipping\Interfaces\ShippingMethodInterface
     */
    public function driver(): ShippingMethodInterface
    {
        return Shipping::driver($this->driver);
    }
}
