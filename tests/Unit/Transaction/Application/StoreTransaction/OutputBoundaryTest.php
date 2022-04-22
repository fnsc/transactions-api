<?php

namespace Transaction\Application\StoreTransaction;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Transaction\Domain\Entities\Transaction;

class OutputBoundaryTest extends TestCase
{
    public function test_should_get_an_instance_from_output_boundary(): void
    {
        // Set
        $transaction = m::mock(Transaction::class);

        // Actions
        $input = new OutputBoundary($transaction);

        // Assertions
        $this->assertInstanceOf(Transaction::class, $input->getTransaction());
    }
}
