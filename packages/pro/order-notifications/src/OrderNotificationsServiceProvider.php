<?php

namespace GetCandy\OrderNotifications;

use GetCandy\Extending\Plugin;
use GetCandy\Hub\Extending\Traits\AdminHubPlugin;
use GetCandy\Hub\Facades\Action;
use GetCandy\Hub\Views\Components\ActivityLog;
use GetCandy\Models\Order;
use GetCandy\OrderNotifications\Actions\OrderNotification;
use GetCandy\OrderNotifications\ActivityLog\EmailNotification;
use GetCandy\OrderNotifications\Http\Livewire\EmailNotification as LivewireEmailNotification;
use GetCandy\OrderNotifications\Http\Livewire\OrderNotificationPreview;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class OrderNotificationsServiceProvider extends ServiceProvider
{
    use AdminHubPlugin;

    public function getActivityLogRenderers()
    {
        return [
            Order::class => [
                EmailNotification::class
            ],
        ];
    }

    // public function register()
    // {
    //     $this->adminHubRegister();
    // }

    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../lang', 'shipping');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'order-notifications');

        $components = [
            OrderNotificationPreview::class,
            LivewireEmailNotification::class,
        ];

        foreach ($components as $component) {
            Livewire::component((new $component())->getName(), $component);
        }

        Action::slot('orders.view.top')->addAction(
            new OrderNotification
        );

    }
}
