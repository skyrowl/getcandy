<?php

namespace GetCandy\Hub\Http\Livewire\Traits;

use GetCandy\Hub\Facades\Action;

trait HasActions
{
    public $currentAction = null;

    public function showAction($action)
    {
        $this->currentAction = $action;
    }

    public function getActions($slot)
    {
        $actions = Action::slot($slot)->getActions();

        $overrides = $actions->pluck('override')->filter();

        return $actions->reject(function ($action) use ($overrides) {
            return $overrides->contains(
                get_class($action)
            );
        });
    }
}
