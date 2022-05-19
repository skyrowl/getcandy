<?php

namespace GetCandy\OrderNotifications\Actions;

use GetCandy\Hub\Actions\Action;
use GetCandy\Hub\Actions\ActionParams;
use GetCandy\Hub\Actions\Orders\UpdateStatus;
use GetCandy\Hub\Http\Livewire\Components\Orders\OrderStatus;

class OrderNotification extends Action
{
    public $override = UpdateStatus::class;

    public function title(): string
    {
        return 'Update the status!';
    }

    public function component(): string
    {
        return 'hub.order-notification.preview';
    }
}
