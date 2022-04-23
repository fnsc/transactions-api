<?php

namespace Transaction\Application\StoreTransaction;

use PHPUnit\Framework\TestCase;

class InputBoundaryTest extends TestCase
{
    public function testShouldGetAnInstanceFromInputBoundary(): void
    {
        // Actions
        $input = new InputBoundary(1, 2, 100.0);

        // Assertions
        $this->assertSame(1, $input->getPayeeId());
        $this->assertSame(2, $input->getPayerId());
        $this->assertSame(100.0, $input->getAmount());
    }
}
