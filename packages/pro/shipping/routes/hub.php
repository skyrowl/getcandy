<?php

use GetCandy\Hub\Http\Middleware\Authenticate;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingIndex;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingZone;
use Illuminate\Support\Facades\Route;

Route::group([
  'prefix'     => config('getcandy-hub.system.path', 'hub'),
  'middleware' => ['web'],
], function () {

    Route::group([
        'middleware' => [
            Authenticate::class,
        ],
        'prefix' => 'shipping',
    ], function () {

        Route::get('/', ShippingIndex::class)->name('hub.shipping.index');
        Route::get('shipping-zones/{id}', ShippingZone::class)->name('hub.shipping.zone');
    });
});
