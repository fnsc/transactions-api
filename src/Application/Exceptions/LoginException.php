<?php

namespace Transaction\Application\Exceptions;

use Exception;

class LoginException extends Exception
{
    public static function invalidData(): self
    {
        return new static('The given data is invalid.');
    }

    public static function userNotFound()
    {
        return new static('User not found.');
    }
}
