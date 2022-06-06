<?php

namespace Transaction\Application\Exceptions;

use Exception;

class FraudException extends Exception
{
    public static function payerIdIsDifferent(): self
    {
        return new static(
            'The payer id is different from the user that is currently authenticated.'
        );
    }

    public static function authorizationDeclined(): self
    {
        return new static('The authorization service declined the operation.');
    }
}
