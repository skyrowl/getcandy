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
        'charge_by' => 'car_total',
    ]
]);
```

### Shipping Method Codes

Aside from being a reference for your storefront, shipping method codes are used to determine related shipping methods across different zones.


## Shipping Method Prices

## Exclusion Lists
