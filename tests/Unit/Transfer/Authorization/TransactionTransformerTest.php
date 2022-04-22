<?php

namespace Transfer\Authorization;

use Mockery as m;
use Money\Money;
use Money\MoneyFormatter;
use Tests\TestCase;
use Transfer\Transaction;
use User\User;

class TransactionTransformerTest extends TestCase
{
    public function test_should_transform_the_given_transaction(): void
    {
        // Set
        $moneyFormatter = m::mock(MoneyFormatter::class);
        $transaction = m::mock(Transaction::class);
        $transformer = new TransactionTransformer($moneyFormatter);
        $money = Money::BRL(100);
        $user = m::mock(User::class);
        $expected = [
            'number' => '61a3c19e16df0e40f8553cb1',
            'payerFiscalDoc' => '12345678906',
            'payeeFiscalDoc' => '12345678906',
            'amount' => '1.00',
        ];

        // Expectations
        $transaction->expects()
            ->getAmount()
            ->andReturn($money);

        $transaction->expects()
            ->getAttribute('number')
            ->andReturn('61a3c19e16df0e40f8553cb1');

        $transaction->expects()
            ->getAttribute('payer')
            ->andReturn($user);

        $transaction->expects()
            ->getAttribute('payee')
            ->andReturn($user);

        $user->expects()
            ->getAttribute('fiscal_doc')
            ->twice()
            ->andReturn('12345678906');

        $moneyFormatter->expects()
            ->format($money)
            ->andReturn('1.00');

        // Actions
        $result = $transformer->transform($transaction);

        // Assertions
        $this->assertSame($expected, $result);
    }
}
