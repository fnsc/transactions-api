<?php

namespace Transaction\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function test_should_get_a_password_instance(): void
    {
        // Actions
        $password = new Password('secret');

        // Assertions
        $this->assertSame('secret', $password->getPlainPassword());
    }
}
