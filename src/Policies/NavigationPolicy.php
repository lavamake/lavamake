<?php

namespace Lavamake\Lavamake\Policies;

use Lavamake\Lavamake\Config\Config;
use Lavamake\Lavamake\Contracts\LavaMakeAuth;

class NavigationPolicy
{
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
     * @param              $nav
     *
     * @return bool
     */
    public function update(LavaMakeAuth $lavaMakeAuth, $nav)
    {
        return $lavaMakeAuth->getLMIdentifier() === $nav->{$this->config->foreignKey()};
    }

    /**
     * delete
     *
     * @param LavaMakeAuth $lavaMakeAuth
     * @param              $nav
     *
     * @return bool
     */
    public function delete(LavaMakeAuth $lavaMakeAuth, $nav)
    {
        return $lavaMakeAuth->getLMIdentifier() === $nav->{$this->config->foreignKey()};
    }

    /**
     * restore
     *
     * @param LavaMakeAuth $lavaMakeAuth
     * @param              $nav
     *
     * @return bool
     */
    public function restore(LavaMakeAuth $lavaMakeAuth, $nav)
    {
        return $lavaMakeAuth->getLMIdentifier() === $nav->{$this->config->foreignKey()};
    }

    /**
     * forceDelete
     *
     * @param LavaMakeAuth $lavaMakeAuth
     * @param              $nav
     *
     * @return bool
     */
    public function forceDelete(LavaMakeAuth $lavaMakeAuth, $nav)
    {
        return $lavaMakeAuth->getLMIdentifier() === $nav->{$this->config->foreignKey()};
    }
}
