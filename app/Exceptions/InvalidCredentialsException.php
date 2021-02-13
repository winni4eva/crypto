<?php

namespace App\Exceptions;

use Tymon\JWTAuth\Exceptions\JWTException;

class InvalidCredentialsException extends JWTException
{
    public function __construct($code = 403, Exception $previous = null)
    {
        parent::__construct('Email and password combination error', $code, $previous);
    }
}
