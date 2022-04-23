<?php

namespace Transaction\Application\Login;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Transaction\Domain\Entities\User;

class OutputBoundaryTest extends TestCase
{
    public function testShouldGetOutputBoundaryInstance(): void
    {
        // Actions
        $result = new OutputBoundary(m::mock(User::class));

        // Assertions
        $this->assertInstanceOf(User::class, $result->getUser());
    }
}
