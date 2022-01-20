<?php

namespace Lavamake\Lavamake\Config;

use Illuminate\Config\Repository;
use Lavamake\Lavamake\Utils\Consts;

final class Config implements ConfigInterface
{
    public $config;

    public function __construct(Repository $config)
    {
        $this->config = $config[Consts::CONFIG_LAVAMAKE];
    }

    public function buildFor()
    {
        return $this->config[Consts::BUILD_FOR];
    }

    public function isBuildForSingle()
    {
        return $this->buildFor() === Consts::BUILD_FOR_SINGLE;
    }

    public function isBuildForPlatform()
    {
        return $this->buildFor() === Consts::BUILD_FOR_PLATFORM;
    }

    public function foreignKey()
    {
        return $this->config[Consts::FOREIGN_KEY];
    }
}
