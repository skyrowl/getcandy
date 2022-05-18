<?php

namespace GetCandy\Hub\Facades;

use GetCandy\Hub\Actions\ActionRegistry;
use Illuminate\Support\Facades\Facade;

class Action extends Facade
{
    /**
     * Return the facade class reference.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ActionRegistry::class;
    }
}
