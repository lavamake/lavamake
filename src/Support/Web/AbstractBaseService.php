<?php

namespace Lavamake\Lavamake\Support\Web;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;

abstract class AbstractBaseService
{
    /**
     * lavamake config
     *
     * @var array|mixed
     */
    protected $config;

    /**
     * current request
     *
     * @var Request
     */
    protected $request;

    public function __construct(Repository $config,Request $request)
    {
        $this->config = $config['lavamake'];
        $this->request = $request;
    }

    protected function foreignKey()
    {
        return $this->config['foreign_key'];
    }
}
