<?php

namespace Lavamake\Lavamake\Facades;

use Illuminate\Support\Facades\Facade;

class WebNav extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lavamake.lavamake.nav.web';
    }
}
