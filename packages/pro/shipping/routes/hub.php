<?php

use GetCandy\Hub\Http\Middleware\Authenticate;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingExclusionListsCreate;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingExclusionListsIndex;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingExclusionListsShow;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingIndex;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingZoneCreate;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingZoneShow;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => config('getcandy-hub.system.path', 'hub'),
    'middleware' => [
        'web',
    ],
], function () {
    Route::group([
        'middleware' => [
            Authenticate::class,
            'can:shipping:manage',
        ],
        'prefix' => 'shipping',
    ], function () {
        Route::get('/', ShippingIndex::class)->name('hub.shipping.index');

        Route::get('shipping-exclusion-lists', ShippingExclusionListsIndex::class)
            ->name('hub.exclusion-lists.index');

        Route::get('shipping-exclusion-lists/create', ShippingExclusionListsCreate::class)
            ->name('hub.exclusion-lists.create');

        Route::get('shipping-exclusion-lists/{list}', ShippingExclusionListsShow::class)
            ->name('hub.exclusion-lists.show');

        Route::get('shipping-zones/create', ShippingZoneCreate::class)->name('hub.shipping-zone.create');
        Route::get('shipping-zones/{shippingZone}', ShippingZoneShow::class)->name('hub.shipping.shipping-zone.show');
    });
});
