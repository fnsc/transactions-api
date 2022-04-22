<?php

namespace Transaction\Domain\Entities;

use Money\Money;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function test_should_get_an_account_instance(): void
    {
        // Actions
        $account = new Account(10000, 1, '123123123', 1);

        // Assertions
        $this->assertSame('10000', $account->getAmount()->getAmount());
        $this->assertInstanceOf(Money::class, $account->getAmount());
        $this->assertSame(1, $account->getUserId());
        $this->assertSame('123123123', $account->getNumber());
        $this->assertSame(1, $account->getId());
    }
}
