# Upgrading

[[toc]]


## General Upgrade Instructions

Update the package

```sh
composer update lunarphp/lunar
```

Run any migrations

```sh
php artisan migrate
```

Re-publish the admin hub assets

```sh
php artisan lunar:hub:install
```

## 0.2

### High Impact

#### Removal of the Cart Manager and Cart Modifiers

This release moves away from Cart Modifiers and the Cart Manager.

You can still extend the Cart in the form of pipelines. See [Cart Extending](extending/carts).

You need to replace any instances of `$cart->getManager()` and just reference the cart itself i.e.

```php
// Old
$cart->getManager()->setShippingOption();

// New
$cart->setShippingOption();
```

For calculating the cart totals you should change the following:

```php
// Old
$cart->getManager()->getCart();

// New
$cart->calculate();
```

## 0.1.0-rc.5

### Changed

#### Publishing changes

All publishing commands for Lunar now use `.` as a separator.

- Rename `lunar-hub-translations` to `lunar.hub.translations`
- Rename `lunar-hub-views` to `lunar.hub.views`
- Rename `lunar:hub:public` to `lunar.hub.public`
- Rename `lunar-migrations` to `lunar.migrations`

#### Brand requirement is now configurable.

Whether the product brand is required on your store is now configurable, the default behaviour is set to `true`. If you wish to change this, simply update `config/lunar-hub/products.php`.

```php
'require_brand' => false,
```

---

## Migrating from GetCandy to Lunar

The initial release of Lunar will be version `0.1.0`. This allows for a rapid development cycle until we reach `1.0.0`.
Understandably, a complete name change is not small task, so we've outlined steps you need to take to bring your install
up to the latest Lunar version and move away from GetCandy.

### Update composer dependencies

```json
"getcandy/admin": "^2.0-beta",
"getcandy/core": "^2.0-beta"
```

```json
"lunarphp/lunar": "^0.1"
```

Any add-ons you are using will need their namespaces updated, the package name should remain the same, i.e.

```json
"getcandy/stripe": "^1.0"
```

```json
"lunarphp/stripe": "^0.1"
```

Once done, remember to run `composer update` to pull in the latest packages.

### Update namespaces

If you are using any GetCandy classes, such as models, you will need to update their namespace:

#### Models

```php
GetCandy\Models\Product;
```

```php
Lunar\Models\Product;
```

A simple find and replace in your code should be sufficient, the strings you should search for are:

```
GetCandy
get-candy
getcandy
```

### Config changes

- Rename the `config/getcandy` folder to `config/lunar`
- Rename the `config/getcandy-hub` folder to `config/lunar-hub`
- Change the prefix in `config/lunar/database.php` from `getcandy_` to `lunar_`

Also make sure any class references in your config files have been updated to the `Lunar` namespace.

### Meilisearch users

Lunar no longer ships with Meilisearch by default. If you use Meilisearch and wish to carry on using it, you will need
to require the new Lunar meilisearch package.

```sh
composer require lunarphp/meilisearch
```

This will install the appropriate packages that Scout needs and also register the set up command so you can keep using
it, you just need to update the signature.

```sh
php artisan lunar:meilisearch:setup
```

### MySQL Search

If you were previously using the `mysql` Scout driver, you should change this to `database_index`. This populates the
`search_index` table with the terms to be searched upon. You may need to run the scout import command:

```sh
php artisan scout:import Lunar\Models\Product
```

### Database migration

If you are using the `getcandy_` prefix in your database, then you will likely want to update this to `lunar_`.
We have created a command for this purpose to try make the switch as easy as possible.

```sh
php artisan lunar:migrate:getcandy
```

#### What this command will do

- Remove any previous GetCandy migrations from the `migrations` table.
- Run the migrations again with the `lunar_` prefix, creating new tables.
- Copy across the data from the old `getcandy_` tables into the new `lunar_` tables.
- Update any polymorphic `GetCandy` classes to the `Lunar` namespace.
- Update field types in `attribute_data` to the `Lunar` namespace.


#### What this command will not do

- Affect any custom tables that have been added outside the core packages.

---

The intention of this is to provide a non-destructive way to migrate the data. Once the command has been run
your `getcandy_` tables should remain intact, so you are free to check the data and remove when ready.
