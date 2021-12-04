<?php

namespace Transfer;

use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class TransferExceptionTest extends TestCase
{
    public function test_should_assert_transfer_exception_cases(): void
    {
        // Actions
        $payerNotFound = TransferException::payerNotFound();
        $payeeNotFound = TransferException::payeeNotFound();
        $accountNotFound = TransferException::accountNotFound();
        $notSufficientAmount = TransferException::notSufficientAmount();
        $notificationWasNotSend = TransferException::notificationWasNotSend(Response::HTTP_INTERNAL_SERVER_ERROR);

        // Assertions
        $this->assertSame('The informed payer was not found on our registers.', $payerNotFound->getMessage());
        $this->assertSame(Response::HTTP_NOT_ACCEPTABLE, $payerNotFound->getCode());

        $this->assertSame('The informed payee was not found on our registers.', $payeeNotFound->getMessage());
        $this->assertSame(Response::HTTP_NOT_ACCEPTABLE, $payeeNotFound->getCode());

        $this->assertSame('The informed account was not found on our registers.', $accountNotFound->getMessage());
        $this->assertSame(Response::HTTP_NOT_ACCEPTABLE, $accountNotFound->getCode());

        $this->assertSame(
            'The payer does not have the sufficient amount on your account to proceed with the operation',
            $notSufficientAmount->getMessage()
        );
        $this->assertSame(Response::HTTP_FORBIDDEN, $notSufficientAmount->getCode());

        $this->assertSame(
            'The user notification was not send due an issue with the provider.',
            $notificationWasNotSend->getMessage()
        );
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $notificationWasNotSend->getCode());
    }
}
