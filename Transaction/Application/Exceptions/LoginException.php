<?php

namespace Transaction\Application\Exceptions;

use Exception;
use Illuminate\Http\Response;

class LoginException extends Exception
{
    public static function invalidData(): self
    {
        return new static('The given data is invalid.', Response::HTTP_UNAUTHORIZED);
    }

    public static function userNotFound()
    {
        return new static('User not found.', Response::HTTP_MOVED_PERMANENTLY);
    }
}
