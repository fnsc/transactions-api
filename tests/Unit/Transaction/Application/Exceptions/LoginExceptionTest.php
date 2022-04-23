<?php

namespace Transaction\Application\Exceptions;

use PHPUnit\Framework\TestCase;

class LoginExceptionTest extends TestCase
{
    public function testShouldAssertTransferExceptionCases(): void
    {
        // Actions
        $invalidData = LoginException::invalidData();
        $userNotFound = LoginException::userNotFound();

        // Assertions
        $this->assertSame('The given data is invalid.', $invalidData->getMessage());
        $this->assertSame('User not found.', $userNotFound->getMessage());
    }
}
