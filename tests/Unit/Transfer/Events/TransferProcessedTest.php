<?php

namespace Transfer\Events;

use DateTime;
use Mockery as m;
use Money\Money;
use Money\MoneyFormatter;
use PHPUnit\Framework\TestCase;
use Transfer\Transaction;
use User\User;

class TransferProcessedTest extends TestCase
{
    public function test_should_get_attributes(): void
    {
        // Set
        $transaction = m::mock(Transaction::class);
        $moneyFormatter = m::mock(MoneyFormatter::class);
        $user = m::mock(User::class);
        $money = Money::BRL(100);
        $expected = [
            'number' => '61a36e0f5437cc30926e0011',
            'payee' => 'Random Name',
            'payer' => 'Random Name',
            'amount' => '1.00',
            'created_at' => '2021-11-28T12:00:00+0000',
        ];

        // Expectations
        $transaction->expects()
            ->getAttribute('number')
            ->andReturn('61a36e0f5437cc30926e0011');

        $transaction->expects()
            ->getAttribute('payee')
            ->andReturn($user);

        $user->expects()
            ->getAttribute('name')
            ->twice()
            ->andReturn('Random Name');

        $transaction->expects()
            ->getAttribute('payer')
            ->andReturn($user);

        $transaction->expects()
            ->getAttribute('created_at')
            ->andReturn(new DateTime('2021-11-28 12:00:00'));

        $transaction->expects()
            ->getAmount()
            ->andReturn($money);

        $moneyFormatter->expects()
            ->format($money)
            ->andReturn('1.00');

        // Actions
        $event = new TransferProcessed($transaction, $moneyFormatter);
        $result = $event->getAttributes();

        // Assertions
        $this->assertSame($expected, $result);
    }
}
