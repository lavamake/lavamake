<?php

namespace Lavamake\Lavamake\Listeners;

use Lavamake\Lavamake\Events\ArticleViewed;
use App\Models\Article;

class ArticleViewedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Menlain\Lavamaker\Events\ArticleViewed  $event
     * @return void
     */
    public function handle(ArticleViewed $event)
    {
        $unicode = $event->article_unicode;
        Article::where(['unicode' => $unicode])->increment('view_number');
    }
}
