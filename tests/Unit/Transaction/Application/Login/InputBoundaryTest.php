<?php

namespace Transaction\Application\Login;

use PHPUnit\Framework\TestCase;

class InputBoundaryTest extends TestCase
{
    public function testShouldGetInputBoundaryInstance(): void
    {
        // Actions
        $result = new InputBoundary('random@email.com', 'secret');

        // Assertions
        $this->assertSame('random@email.com', $result->getEmail());
        $this->assertSame('secret', $result->getPassword());
    }
}
