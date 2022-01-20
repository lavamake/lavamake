<?php

namespace Lavamake\Lavamake\Config;

interface ConfigInterface
{
    public function buildFor();

    public function foreignKey();
}
