<?php

namespace Transaction\Application\Exceptions;

use PHPUnit\Framework\TestCase;

class FraudExceptionTest extends TestCase
{
    public function test_should_throw_an_exception_when_payer_is_different(): void
    {
        // Actions
        $exception = FraudException::payerIdisDifferent();

        // Assertions
        $this->assertSame(
            'The payer id is different from the user that is currently authenticated.',
            $exception->getMessage()
        );
    }

    public function test_should_throw_an_exception_when_authorization_was_declined(): void
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
