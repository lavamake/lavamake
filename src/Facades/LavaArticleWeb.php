<?php

namespace Lavamake\Lavamake\Facades;

use Illuminate\Support\Facades\Facade;
use App\Models\Article;

/**
 * @method static Article show($unicode, $foreign_key_value = 0)
 */
class LavaArticleWeb extends Facade
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
