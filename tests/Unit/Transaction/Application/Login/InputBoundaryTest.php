<?php

namespace Transaction\Application\Login;

use PHPUnit\Framework\TestCase;

class InputBoundaryTest extends TestCase
{
    public function test_should_get_input_boundary_instance(): void
    {
        // Actions
        $result = new InputBoundary('random@email.com', 'secret');

        // Assertions
        $this->assertSame('random@email.com', $result->getEmail());
        $this->assertSame('secret', $result->getPassword());
    }
}
