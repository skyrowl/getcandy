<?php

namespace GetCandy\Shipping\Models;

use GetCandy\Base\BaseModel;
use GetCandy\Models\Country;
use GetCandy\Shipping\Factories\ShippingZoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends BaseModel
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
     * Return the shipping methods relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingMethods()
    {
        return $this->hasMany(ShippingMethod::class);
    }

    /**
     * Return the countries relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function countries()
    {
        return $this->belongsToMany(
            Country::class,
            config('getcandy.database.table_prefix') . 'country_shipping_zone'
        )->withTimestamps();
    }

    /**
     * Return the postcodes relationship
     *
     * @return HasMany
     */
    public function postcodes()
    {
        return $this->hasMany(ShippingZonePostcode::class);
    }
}
