<?php

namespace Transaction\Application\Exceptions;

use PHPUnit\Framework\TestCase;

class FraudExceptionTest extends TestCase
{
    public function testShouldThrowAnExceptionWhenPayerIsDifferent(): void
    {
        // Actions
        $exception = FraudException::payerIdIsDifferent();

        // Assertions
        $this->assertSame(
            'The payer id is different from the user that is currently authenticated.',
            $exception->getMessage()
        );
    }

    public function testShouldThrowAnExceptionWhenAuthorizationWasDeclined(): void
    {
        // Actions
        $exception = FraudException::authorizationDeclined();

        // Assertions
        $this->assertSame(
            'The authorization service declined the operation.',
            $exception->getMessage()
        );
    }
}
