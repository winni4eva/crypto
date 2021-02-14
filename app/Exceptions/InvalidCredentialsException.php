<?php

namespace App\Exceptions;

use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

class InvalidCredentialsException extends JWTException
{
    public function __construct($code = 403, Throwable $previous = null)
    {
        parent::__construct('Email and password combination error', $code, $previous);
    }
}
