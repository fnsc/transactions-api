<?php

namespace Transaction\Application\Exceptions;

use Exception;
use Illuminate\Http\Response;

class TransferException extends Exception
{
    public static function payerNotFound(): self
    {
        return new static('The informed payer was not found on our registers.', Response::HTTP_NOT_ACCEPTABLE);
    }

    public static function payeeNotFound(): self
    {
        return new static('The informed payee was not found on our registers.', Response::HTTP_NOT_ACCEPTABLE);
    }

    public static function accountNotFound(): self
    {
        return new static('The informed account was not found on our registers.', Response::HTTP_NOT_ACCEPTABLE);
    }

    public static function notSufficientAmount(): self
    {
        return new static(
            'The payer does not have the sufficient amount on your account to proceed with the operation',
            Response::HTTP_FORBIDDEN
        );
    }

    public static function notificationWasNotSend(int $statusCode): self
    {
        return new static('The user notification was not send due an issue with the provider.', $statusCode);
    }
}
