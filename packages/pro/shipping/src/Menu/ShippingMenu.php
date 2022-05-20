<?php

namespace GetCandy\Shipping\Menu;

use GetCandy\Hub\Facades\Menu;

class ShippingMenu
{
    /**
     * Make our menu.
     *
     * @return void
     */
    public static function make()
    {
        (new static())
            ->makeTopLevel();
    }

    /**
     * Create our top level menu.
     *
     * @return static
     */
    protected function makeTopLevel()
    {
        $slot = Menu::slot('shipping');

        $slot->addItem(function ($item) {
            $item->name('Shipping Zones')
                ->handle('hub.shipping')
                ->route('hub.shipping.index')
                ->gate('shipping:manage')
                ->icon('globe');
        });

        $slot->addItem(function ($item) {
            $item->name('Shipping Exclusions')
                ->handle('hub.exclusion-lists')
                ->route('hub.exclusion-lists.index')
                ->gate('shipping:manage')
                ->icon('archive');
        });

        return $this;
    }
}
