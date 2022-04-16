<?php

namespace Transaction;

use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Transaction\Application\StoreTransaction\FraudException;

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
        $this->assertSame(Response::HTTP_NOT_ACCEPTABLE, $exception->getCode());
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
        $this->assertSame(Response::HTTP_NOT_ACCEPTABLE, $exception->getCode());
    }
}
