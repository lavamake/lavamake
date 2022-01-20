<?php

namespace Lavamake\Lavamake\Article;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Lavamake\Lavamake\Config\ConfigInterface;
use Lavamake\Lavamake\Utils\Consts;

abstract class ArticleAbstract
{
    protected $config;
    protected $request;
    protected $article;

    public function __construct(Model $article, ConfigInterface $config, Request $request)
    {
        $this->config = $config;
        $this->request = $request;
        $this->article = $article;
    }

    protected function condition($where = [], $user_id = 0)
    {
        $build_for = $this->config->buildFor();

        if($build_for == Consts::BUILD_FOR_PLATFORM) {
            return array_merge($where, [$this->config->foreignKey() => $user_id]);
        } else {
            return $where;
        }
    }
}
