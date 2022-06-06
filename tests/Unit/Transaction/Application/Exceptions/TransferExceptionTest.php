<?php

namespace Transaction\Application\Exceptions;

use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class TransferExceptionTest extends TestCase
{
    public function testShouldAssertTransferExceptionCases(): void
    {
        // Actions
        $payerNotFound = TransferException::payerNotFound();
        $payeeNotFound = TransferException::payeeNotFound();
        $accountNotFound = TransferException::accountNotFound();
        $notSufficientAmount = TransferException::notSufficientAmount();
        $notificationWasNotSend = TransferException::notificationWasNotSend(
            Response::HTTP_BAD_REQUEST
        );

        // Assertions
        $this->assertSame(
            'The informed payer was not found on our registers.',
            $payerNotFound->getMessage()
        );
        $this->assertSame(
            'The informed payee was not found on our registers.',
            $payeeNotFound->getMessage()
        );
        $this->assertSame(
            'The informed account was not found on our registers.',
            $accountNotFound->getMessage()
        );
        $this->assertSame(
            'The payer does not have the sufficient amount on your account to proceed with the operation',
            $notSufficientAmount->getMessage()
        );
        $this->assertSame(
            'The user notification was not send due an issue with the provider.',
            $notificationWasNotSend->getMessage()
        );
    }
}
