<?php

namespace Lavamake\Lavamake\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static showPost($unicode, $foreign_key_value = 0)
 *
 * @method static string renderNavRootWithHtml($user_id = 0)
 * @method static string renderNavRoot($user_id = 0)
 * @method static string renderNavChild($pid = 0, $user_id = 0)
 * @method static string renderNavs($pid = 0, $with_child = false, $user_id = 0)
 * @method static navigation($unicode, $user_id = 0)
 * @method static navArticles($navigation_id, $page = 1, $limit = 0, $user_id = 0)
 * @method static brotherNavs($navigation_pid = 0, $user_id = 0)
 * @method static parentNavs($navigation_pid = 0, $user_id = 0)
 */
class WebArticle extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lavamake.lavamake.article.web';
    }
}
