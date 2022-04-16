<?php

namespace Transaction\Application\Store;

use Exception;

class FraudException extends Exception
{
    public static function payerIdisDifferent(): self
    {
        return new static('The payer id is different from the user that is currently authenticated.');
    }

    public static function authorizationDeclined(): self
    {
        return new static('The authorization service declined the operation.');
    }
}
