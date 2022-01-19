<?php

namespace Lavamake\Lavamake\Contracts;

use App\Models\Article;
use Lavamake\Lavamake\Support\Consts;

trait ArticleHelper
{
    protected $config;

    protected $user;

    public function requestUser()
    {
        return $this->user;
    }

    /**
     * Published Article Number
     *
     * @return mixed
     *
     * by Menlain
     * 2022/1/18 - 7:48 AM
     */
    public function userPublishedArticleNumber()
    {
        return Article::where([
            $this->config['foreign_key'] => $this->user->getLMIdentifier(),
            'status' => Consts::PUBLISHED
        ])->count();
    }

    /**
     * Draft Article Number
     *
     * @return mixed
     *
     * by Menlain
     * 2022/1/18 - 7:48 AM
     */
    public function userDraftArticleNumber()
    {
        return Article::where([
            $this->config['foreign_key'] => $this->user->getLMIdentifier(),
            'status' => Consts::DRAFT
        ])->count();
    }

    /**
     * All article Number(published and draft)
     *
     * @return mixed
     *
     * by Menlain
     * 2022/1/18 - 7:49 AM
     */
    public function userArticleNumber()
    {
        return Article::where([
            $this->config['foreign_key'] => $this->user->getLMIdentifier(),
        ])->count();
    }
}
