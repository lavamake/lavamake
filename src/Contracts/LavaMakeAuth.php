<?php

namespace Lavamake\Lavamake\Contracts;

interface LavaMakeAuth
{
    /**
     * Get the identifier that will be stored in the subject of the Lava-maker.
     *
     * @return mixed
     */
    public function getLMIdentifier(): mixed;

    /**
     * The current user is admin or not
     *
     * @return bool
     *
     * by Menlain
     * 2022/1/18 - 5:08 AM
     */
    public function isLMAdmin(): bool;
}
