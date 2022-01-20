<?php

namespace Lavamake\Lavamake\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * @method static integer userArticleNumber()
 * @method static integer userPublishedArticleNumber()
 * @method static integer userDraftArticleNumber()
 * @method static integer userTrashedArticleNumber()
 *
 * @method static array createArticle($request)
 */
class ManageArticle extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lavamake.lavamake.article.manage';
    }
}
