<?php

namespace Lavamake\Lavamake\Events;

class ArticleViewed
{

    public $article_unicode;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($article_unicode)
    {
        $this->article_unicode = $article_unicode;
    }
}
