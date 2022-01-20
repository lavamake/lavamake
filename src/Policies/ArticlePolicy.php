<?php

namespace Lavamake\Lavamake\Policies;

use Lavamake\Lavamake\Config\Config;
use Lavamake\Lavamake\Contracts\LavaMakeAuth;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function before(LavaMakeAuth $lavaMakerAuth)
    {
        return $lavaMakerAuth->isLMAdmin();
    }

    /**
     * update
     *
     * @param LavaMakeAuth $lavaMakeAuth
     * @param              $article
     *
     * @return bool
     */
    public function update(LavaMakeAuth $lavaMakeAuth, $article)
    {
        return $lavaMakeAuth->getLMIdentifier() === $article->{$this->config->foreignKey()};
    }

    /**
     * publish
     *
     * @param LavaMakeAuth $lavaMakeAuth
     * @param              $article
     *
     * @return bool
     */
    public function publish(LavaMakeAuth $lavaMakeAuth, $article)
    {
        return $lavaMakeAuth->getLMIdentifier() === $article->{$this->config->foreignKey()};
    }

    /**
     * draft
     *
     * @param LavaMakeAuth $lavaMakeAuth
     * @param              $article
     *
     * @return bool
     */
    public function draft(LavaMakeAuth $lavaMakeAuth, $article)
    {
        return $lavaMakeAuth->getLMIdentifier() === $article->{$this->config->foreignKey()};
    }

    /**
     * delete
     *
     * @param LavaMakeAuth $lavaMakeAuth
     * @param              $article
     *
     * @return bool
     */
    public function delete(LavaMakeAuth $lavaMakeAuth, $article)
    {
        return $lavaMakeAuth->getLMIdentifier() === $article->{$this->config->foreignKey()};
    }

    /**
     * restore
     *
     * @param LavaMakeAuth $lavaMakeAuth
     * @param              $article
     *
     * @return bool
     */
    public function restore(LavaMakeAuth $lavaMakeAuth, $article)
    {
        return $lavaMakeAuth->getLMIdentifier() === $article->{$this->config->foreignKey()};
    }

    /**
     * forceDelete
     *
     * @param LavaMakeAuth $lavaMakeAuth
     * @param              $article
     *
     * @return bool
     */
    public function forceDelete(LavaMakeAuth $lavaMakeAuth, $article)
    {
        return $lavaMakeAuth->getLMIdentifier() === $article->{$this->config->foreignKey()};
    }
}
