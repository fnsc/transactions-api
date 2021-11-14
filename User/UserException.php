<?php

namespace User;

use Exception;
use Illuminate\Http\Response;

class UserException extends Exception
{
    public static function failedStoring(): self
    {
        return new static('The new user cannot be stored.', Response::HTTP_SERVICE_UNAVAILABLE);
    }

    public static function emailAlreadyExists(): self
    {
        return new static('The email has already been taken.', Response::HTTP_CONFLICT);
    }

    public static function fiscalDocAlreadyExists(): self
    {
        return new static('The fiscal doc has already been taken.', Response::HTTP_CONFLICT);
    }

    public static function invalidUserType(): self
    {
        return new static('The user type is invalid.', Response::HTTP_NOT_ACCEPTABLE);
    }
}
