<?php

namespace Lavamake\Lavamake\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ResourceNotFoundException extends HttpException
{
    public function __construct(?int $code = -1, ?string $message = '',  \Throwable $previous = null, array $headers = [])
    {
        $statusCode = 404;
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
