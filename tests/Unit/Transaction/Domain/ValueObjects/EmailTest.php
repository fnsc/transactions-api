<?php

namespace Transaction\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testShouldGetAnEmailInstance(): void
    {
        // Actions
        $email = new Email('random@email.com');

        // Assertions
        $this->assertSame('random@email.com', (string) $email);
    }
}
