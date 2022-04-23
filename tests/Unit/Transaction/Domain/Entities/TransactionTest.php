<?php

namespace Transaction\Domain\Entities;

use Money\Money;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testShouldGetATransactionInstance(): void
    {
        // Actions
        $transaction = new Transaction(
            User::newUser(),
            User::newUser(),
            10000,
            '1231231231'
        );

        // Assertions
        $this->assertInstanceOf(User::class, $transaction->getPayer());
        $this->assertInstanceOf(User::class, $transaction->getPayee());
        $this->assertInstanceOf(Money::class, $transaction->getAmount());
        $this->assertSame('1231231231', $transaction->getNumber());
    }
}
