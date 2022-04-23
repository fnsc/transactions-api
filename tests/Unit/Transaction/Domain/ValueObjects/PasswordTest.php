<?php

namespace Transaction\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testShouldGetAPasswordInstance(): void
    {
        // Actions
        $password = new Password('secret');

        // Assertions
        $this->assertSame('secret', $password->getPlainPassword());
    }
}
