<?php

namespace Transaction\Application\Events;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Transaction\Domain\Entities\Transaction;

class TransferProcessedTest extends TestCase
{
    public function testShouldGetAttributes(): void
    {
        // Set
        $transaction = m::mock(Transaction::class);

        // Actions
        $event = new TransferProcessed($transaction);

        // Assertions
        $this->assertInstanceOf(Transaction::class, $event->getTransaction());
    }
}
