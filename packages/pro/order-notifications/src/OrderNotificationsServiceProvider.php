<?php

namespace GetCandy\OrderNotifications;

use GetCandy\Hub\Facades\Action;
use GetCandy\Hub\Facades\ActivityLog;
use GetCandy\Models\Order;
use GetCandy\OrderNotifications\Actions\OrderNotification;
use GetCandy\OrderNotifications\ActivityLog\EmailNotification;
use GetCandy\OrderNotifications\Http\Livewire\EmailNotification as LivewireEmailNotification;
use GetCandy\OrderNotifications\Http\Livewire\OrderNotificationPreview;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class OrderNotificationsServiceProvider extends ServiceProvider
{
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

        ActivityLog::addRender(Order::class, EmailNotification::class);
    }
}
