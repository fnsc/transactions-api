<?php

namespace Transaction\Application\Exceptions;

use Exception;

class TransferException extends Exception
{
    public static function payerNotFound(): self
    {
        return new static('The informed payer was not found on our registers.');
    }

    public static function payeeNotFound(): self
    {
        return new static('The informed payee was not found on our registers.');
    }

    public static function accountNotFound(): self
    {
        return new static('The informed account was not found on our registers.');
    }

    public static function notSufficientAmount(): self
    {
        return new static('The payer does not have the sufficient amount on your account to proceed with the operation');
    }

    public static function notificationWasNotSend(int $statusCode): self
    {
        return new static('The user notification was not send due an issue with the provider.', $statusCode);
    }
}
