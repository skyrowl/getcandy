<?php

namespace GetCandy\Shipping;

use GetCandy\Hub\Facades\Menu;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingIndex;
use GetCandy\Shipping\Http\Livewire\Pages\ShippingZone;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class ShippingServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'shipping');

        $slot = Menu::slot('sidebar');

        $slot->addItem(function ($item) {
            $item->name(
                __('shipping::index.menu_item')
            )->handle('hub.tickets')
            ->route('hub.shipping.index')
            ->icon('truck');
        });
        // // Register our payment type.
        // Payments::extend('opayo', function ($app) {
        //     return $app->make(OpayoPaymentType::class);
        // });

        // $this->app->singleton(OpayoInterface::class, function ($app) {
        //     return $app->make(Opayo::class);
        // });

        // $this->mergeConfigFrom(__DIR__."/../config/opayo.php", "getcandy.opayo");

        $this->loadRoutesFrom(__DIR__."/../routes/hub.php");

        // Blade::directive('opayoScripts', function () {
        //     $url = 'https://pi-test.sagepay.com/api/v1/js/sagepay.js';

        //     $manifest = json_decode(file_get_contents(__DIR__.'/../dist/mix-manifest.json'), true);

        //     $jsUrl = asset('/vendor/opayo'.$manifest['/opayo.js']);

        //     if (config('services.opayo.env', 'test') == 'live') {
        //         $url = 'https://pi-live.sagepay.com/api/v1/js/sagepay.js';
        //     }

        //     $manifest = json_decode(file_get_contents(__DIR__.'/../dist/mix-manifest.json'), true);

        //     $jsUrl = asset('/vendor/getcandy'.$manifest['/opayo.js']);

        //     return  <<<EOT
        //         <script src="{$jsUrl}"></script>
        //         <script src="{$url}"></script>
        //     EOT;
        // });

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'shipping');

        // dd(realpath(__DIR__.'/../lang'));


        // $this->publishes([
        //     __DIR__."/../config/opayo.php" => config_path("getcandy/opayo.php"),
        // ], 'getcandy.opayo.config');

        // $this->publishes([
        //     __DIR__.'/../resources/views' => resource_path('views/vendor/getcandy'),
        // ], 'getcandy.opayo.components');

        // $this->publishes([
        //     __DIR__.'/../dist' => public_path('vendor/getcandy'),
        // ], 'getcandy.opayo.public');

        // // Register the stripe payment component.
        Livewire::component('get-candy.shipping.http.livewire.pages.shipping-index', ShippingIndex::class);
        Livewire::component('get-candy.shipping.http.livewire.pages.shipping-zone', ShippingZone::class);
    }
}
