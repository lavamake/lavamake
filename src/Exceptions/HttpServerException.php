<?php

namespace Lavamake\Lavamake\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpServerException extends HttpException
{
    public function __construct(?string $message = '', \Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        $statusCode = 500;
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
