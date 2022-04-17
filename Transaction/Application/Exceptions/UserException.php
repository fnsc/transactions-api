<?php

namespace Transaction\Application\Exceptions;

use Exception;

class UserException extends Exception
{
    public static function failedStoring(): self
    {
        return new static('The new user cannot be stored.');
    }

    public static function emailAlreadyExists(): self
    {
        return new static('The email has already been taken.');
    }

    public static function fiscalDocAlreadyExists(): self
    {
        return new static('The fiscal doc has already been taken.');
    }

    public static function invalidUserType(): self
    {
        return new static('The user type is invalid.');
    }
}
