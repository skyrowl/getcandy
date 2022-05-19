<?php

namespace GetCandy\OrderNotifications\ActivityLog;

use GetCandy\Hub\Base\ActivityLog\AbstractRender;
use Spatie\Activitylog\Models\Activity;

class EmailNotification extends AbstractRender
{
    public function getEvent(): string
    {
        return 'email-notification';
    }

    public function render(Activity $log)
    {
        return view('order-notifications::email-notification', [
            'log' => $log,
        ]);
    }
}
