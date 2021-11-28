<?php

namespace Transfer;

use Exception;
use Illuminate\Http\Response;

class FraudException extends Exception
{
    public static function payerIdisDifferent(): self
    {
        return new static(
            'The payer id is different from the user that is currently authenticated.',
            Response::HTTP_NOT_ACCEPTABLE
        );
    }

    public static function authorizationDeclined(): self
    {
        return new static('The authorization service declined the operation.', Response::HTTP_NOT_ACCEPTABLE);
    }
}
