<?php

namespace GetCandy\Hub\Actions\Orders;

use GetCandy\Hub\Actions\Action;
use GetCandy\Hub\Actions\ActionParams;
use GetCandy\Hub\Http\Livewire\Components\Orders\OrderStatus;


class UpdateStatus extends Action
{
    public function title(): string
    {
        return 'Update the status!';
    }

    public function component(): string
    {
        return 'hub.components.orders.status';
    }
}
