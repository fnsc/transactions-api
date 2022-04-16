<?php

namespace Transaction;

use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use User\LoginException;

class LoginExceptionTest extends TestCase
{
    public function test_should_assert_transfer_exception_cases(): void
    {
        // Actions
        $invalidData = LoginException::invalidData();
        $userNotFound = LoginException::userNotFound();

        // Assertions
        $this->assertSame('The given data is invalid.', $invalidData->getMessage());
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $invalidData->getCode());

        $this->assertSame('User not found.', $userNotFound->getMessage());
        $this->assertSame(Response::HTTP_MOVED_PERMANENTLY, $userNotFound->getCode());
    }
}
