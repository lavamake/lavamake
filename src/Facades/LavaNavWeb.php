<?php

namespace Lavamake\Lavamake\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string renderRootWithHtml($user_id = 0)
 * @method static string renderRoot($user_id = 0)
 * @method static string renderChild($pid = 0, $user_id = 0)
 * @method static string render($pid = 0, $with_child = false, $user_id = 0)
 * @method static navigation($unicode, $user_id = 0)
 * @method static articles($navigation_id, $page = 1, $limit = 0, $user_id = 0)
 * @method static brothers($navigation_pid = 0, $user_id = 0)
 * @method static parent($navigation_pid = 0, $user_id = 0)
 */
class LavaNavWeb extends Facade
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
