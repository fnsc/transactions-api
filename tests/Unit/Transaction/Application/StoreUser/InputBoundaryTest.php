<?php

namespace Transaction\Application\StoreUser;

use PHPUnit\Framework\TestCase;

class InputBoundaryTest extends TestCase
{
    public function testShouldGetAnInstanceFromInputBoundary(): void
    {
        // Actions
        $input = new InputBoundary(
            'random name',
            'random@email.com',
            '12345678909',
            'regular',
            'secret'
        );

        // Assertions
        $this->assertSame('random name', $input->getName());
        $this->assertSame('random@email.com', $input->getEmail());
        $this->assertSame('12345678909', $input->getRegistrationNumber());
        $this->assertSame('regular', $input->getType());
        $this->assertSame('secret', $input->getPassword());
    }
}
