<?php

namespace GetCandy\OrderNotifications\Http\Livewire;

use GetCandy\Hub\Http\Livewire\Traits\Notifies;
use GetCandy\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\Activitylog\Contracts\Activity;

class EmailNotification extends Component
{
    public $showPreview = false;

    public Activity $log;

    public function getPreviewHtmlProperty()
    {
        return Storage::get(
            $this->log->getExtraProperty('template')
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function getName()
    {
        return 'hub.order-notification.email-notification';
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        return view('order-notifications::email-notification-log');
    }
}
