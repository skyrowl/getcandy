# Shipping

[[toc]]

::: warning This is a PRO feature
Shipping functionality is a PRO feature and although you are free to use this when in development, once you go Live you will need a PRO licence.
:::

## Overview

We've made a design choice to have activity logging throughout GetCandy when it comes to changes happening on Eloquent models. We believe it's important to keep track of what updates are happening and who is making them. It allows us to provide you with an invaluable insight into what's happening in your store.

## Shipping Zones

Shipping Zones are geographic area's you want to represent when making certain Shipping Methods available to the customer on checkout. Shipping Zones can support the following types:

- `unrestricted` - This zone is available to all customers
- `postcodes` - Limit this zone to a list of postcodes
- `countries` - Limit this zone to a list of countries
- `states` - Limit this zone to a list of states

### ShippingZone

```php
use GetCandy\Shipping\Models\ShippingZone;
```

|Field|Description|
|:-|:-|
|`id`||
|`name`|`string`|
|`type`|`string`|
|`created_at`||
|`updated_at`||

```php
ShippingZone::create([
    'name' => 'United Kingdom',
    'type' => 'countries',
]);

ShippingZone::create([
    'name' => 'Mainland England',
    'type' => 'postcodes',
]);

ShippingZone::create([
    'name' => 'East Anglia',
    'type' => 'states',
]);
```

## ShippingZonePostcode

If you want to restrict the Shipping Zone to only certain postcodes, you can assign `ShippingZonePostcode` models to your zone, this means this zone will only be valid when a postcode matches. To prevent postcodes from different countries matching, you should also attach a country.

```php
use GetCandy\Shipping\Models\ShippingZonePostcode;
```

|Field|Description|
|:-|:-|
|`id`||
|`shipping_zone_id`||
|`postcode`|`string`|
|`created_at`||
|`updated_at`||

```php
ShippingZonePostcode::create([
    'shipping_zone_id' => 1,
    'postcode' => 'NW12TX',
]);

$shippingZone->postcodes()->create([
    'postcode' => 'NW12TX',
]);

// Only allow postcodes from United Kingdom
$country = Country::where('iso3', '=', 'GBR')->first();
$shippingZone->countries()->sync([$country->id]);
```

## Shipping Zone Countries

You might decide you want a Zone to be available across a whole country, useful for example if you wanted to allow United Kingdom without having to enter every single possible postcode.

```php
$shippingZone->countries()->sync([ /** Country ids **/ ]);
```

## Shipping Methods

Shipping Methods are what you present to the customer at checkout i.e. Standard Delivery. They are what have prices associated, restricted product lists and various configuration based on their needs.

Each shipping method has an underlying driver which powers it, the current ones that Shipping comes with out the box are:

|Name|Driver Key|Description|
|:-|:-|:-|:-|
|Free Shipping|`free-shipping`| Provide free shipping to customers
|Flat Rate|`flat-rate`| Define a set cost for shipping based on each item or cart total.
|Ship by|`ship-by`| Define a set cost for shipping based on each item or cart total.
|Collection|`collection`| Allow customers to pick up their order from a physical location.

You are free to add your own drivers as your store requires. See extending shipping

```php
use GetCandy\Shipping\Models\ShippingMethod;
```

|Field|Description|
|:-|:-|
|`id`||
|`shipping_zone_id`||
|`name`|A custom name for the shipping method, to display on checkout.|
|`description`|A custom description for the shipping method, to display on checkout.|
|`code`|A Code to represent this shipping method|
|`enabled`| Whether to enable this shipping method for this zone|
|`data`| Additional JSON formatted data|
|`driver`| The shipping driver to use|s
|`created_at`||
|`updated_at`||

```php
ShippingMethod::create([
    'name' => 'Standard Delivery',
    'description' => 'Ships in 2-3 days',
    'driver' => 'ship-by',
    'enabled' => true,
    'code' => 'STNDRD',
    'data' => [
        'charge_by' => 'cart_total',
    ]
]);
```

::: tip
All shipping methods can have prices associated to them, although not all drivers will use them.
:::

### Shipping Method Codes

Aside from being a reference for your storefront, shipping method codes are used to determine related shipping methods across different zones.


## Shipping Method Prices

## Exclusion Lists

Sometimes you might not want certain products to be available for shipping in a Shipping Method, this is where Shipping Exclusion Lists come in. Once you have created an exclusion list, they can be assigned to any Shipping Method and then if a users cart contains any of those items, the Shipping Method will not be returned on the Checkout.

```php
use GetCandy\Shipping\Models\ShippingExclusionList;
```

|Field|Description|
|:-|:-|
|`id`||
|`name`||
|`created_at`||
|`updated_at`||

```php
$list = ShippingExclusionList::create([
    'name' => 'Oversized Products'
]);
```

Attaching purchasable items to the list

```php
// GetCandy\Shipping\Models\ShippingExclusion
$list->exclusions()->create([
    'purchasable_type' => 'GetCandy\Models\Product',
    'purchasable_id' => 1,
]);
```

Attaching to a shipping method.

```php
$shippingMethod = ShippingMethod::create([/* .. */]);

$shippingMethod->shippingExclusions()->sync([$list->id]);
```


## Available Shipping Methods

### Free Shipping

```php
ShippingMethod::create([
    'name' => 'Free Shipping',
    'code' => 'FREESHIPPING',
    'description' => '...',
    'driver' => 'free-shipping',
    'enabled' => true,
    'data' => [
        'minimum_spend' => 500,
        'use_discount_amount' => false,
    ]
]);
```

- **minimum_spend** - The minimum amount in cents to qualify for free shipping.
- **use_discount_amount** - If `true` the cart sub total minus any discounts will be used to check for eligibility.

### Ship by

The Ship By driver allows you to specify how you want shipping prices to be calculated. Available options are `cart_total` and `weight`.

```php
$method = ShippingMethod::create([
    'name' => 'Ship By',
    'code' => 'SHIPBY',
    'description' => '...',
    'driver' => 'ship-by',
    'enabled' => true,
]);

// Add a default rate
$method->prices()->create([
    'tier' => 1,
    'price' => 5000,
]);
```

#### Pricing Tiers

For the Ship By driver, you can add additional pricing tiers which will be based on either `cart_total` or `weight` where the `tier` column references the calculated total of that field.


```php
$method->prices()->createMany([
    [
        'tier' => 5000,
        'price' => 4000,
        'customer_group_id' => null,
    ],
    [
        'tier' => 6000,
        'price' => 0
        'customer_group_id' => null,
    ]
])
```

Assuming we are calculating via `cart_total`, if the total is `5000` ($5) then the price is `4000` ($4). If the price is `6000` ($6) then the price is `0`.
If none of the tiers are met, then the price will be the default `5000` ($5)

### Flat Rate

Flat rate shipping methods allow you to specify a set price for shipping across customer groups or for all customers.

```php
$method = ShippingMethod::create([
    'name' => 'Flat Rate',
    'code' => 'FLATRATE',
    'description' => '...',
    'driver' => 'flat-rate',
    'enabled' => true,
    'data' => [
        'charge_by' => 'cart_total',
    ]
]);
```

```php
$method->prices()->createMany([
    [
        'tier' => 1,
        'price' => 4000,
        'customer_group_id' => null,
    ],
])
```

### Collection

If you want to allow customers to pick up their order in store, you can add `collection` shipping methods. These generally do not have any pricing associated to them.

```php
$method = ShippingMethod::create([
    'name' => 'Collection',
    'code' => 'COLLECTION',
    'description' => '...',
    'driver' => 'collection',
    'enabled' => true,
]);
```

## Checkout usage

The shipping add-on will automatically register itself with the ShippingManifest, so the shipping options will already be available via.

```php
GetCandy\Facades\ShippingManifest::getOptions($this->cart)
```

## Using the Shipping Facacde


### Shipping Zones by country

```php
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Models\Country;

Shipping::zones()->country(Country $country)->get();
```

### Shipping Zones by state

```php
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Models\State;

Shipping::zones()->state(State $state)->get();
```

### Shipping Zones by postcode

When fetching by postcode, we still need to pass a country in case the same postcode exists across multiple countries.

```php
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Models\Country;

Shipping::zones()->country(Country $country)->postcode('AB1 1CD')->get();
```


### Get Shipping Methods

```php
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Models\Cart;

Shipping::methods()->cart(Cart $cart)->get();
```

In order to use the `cart` method, we're assuming the cart has a shipping address associated to it, in most cases this is fine, but you might want to offer up a shipping estimate based on country/postcode, for this you can use the individual methods.

```php
Shipping::methods()->state('Essex')->get();

$country = \GetCandy\Models\Country::first();

Shipping::methods()->country($country)->get();

Shipping::methods()->country($country)->postcode('AB1 12C')->get();
```

