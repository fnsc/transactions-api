<?php

namespace Transfer;

use Money\Money;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function test_should_return_a_belongs_to_payer_relationship(): void
    {
        // Set
        $transaction = new Transaction();
        $transaction->amount = 10000;

        // Actions
        $result = $transaction->getAmount();

        // Assertions
        $this->assertInstanceOf(Money::class, $result);
    }
}
