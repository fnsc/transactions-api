<?php

namespace Transaction;

use Money\Money;
use PHPUnit\Framework\TestCase;
use Transaction\Infra\Eloquent\Account;

class AccountTest extends TestCase
{
    public function test_should_return_a_money_instance(): void
    {
        // Set
        $data = [
            'user_id' => 1,
            'number' => 'naAtgqEzVF9VLimG',
            'amount' => 100,
        ];
        $account = new Account($data);

        // Actions
        $result = $account->getAmount();

        // Assertions
        $this->assertInstanceOf(Money::class, $result);
        $this->assertSame(100, (int) $result->getAmount());
    }
}
