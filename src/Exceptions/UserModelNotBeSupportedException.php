<?php

namespace Lavamake\Lavamake\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UserModelNotBeSupportedException extends HttpException
{
    public function __construct(?string $message = '', ?int $code = -1,  \Throwable $previous = null, array $headers = [])
    {
        $statusCode = 403;
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
