<?php

namespace Transaction\Infra\Transformers;

use Mockery as m;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Transaction\Domain\Entities\Transaction as TransactionEntity;
use Transaction\Domain\Entities\User as UserEntity;

class TransactionTest extends TestCase
{
    public function test_should_transform_the_transaction(): void
    {
        // Set
        $transaction = m::mock(TransactionEntity::class);
        $transformer = new Transaction();
        $payer = m::mock(UserEntity::class);
        $payee = m::mock(UserEntity::class);

        // Expectations
        $transaction->expects()
            ->getPayer()
            ->andReturn($payer);

        $payer->expects()
            ->getName()
            ->andReturn('Payer Name');

        $transaction->expects()
            ->getPayee()
            ->andReturn($payee);

        $payee->expects()
            ->getName()
            ->andReturn('Payee Name');

        $transaction->expects()
            ->getAmount()
            ->andReturn(Money::BRL(10000));

        // Actions
        $result = $transformer->transform($transaction);

        // Assertions
        $this->assertSame([
            'payer' => 'Payer Name',
            'payee' => 'Payee Name',
            'amount' => '10000',
        ], $result);
    }
}
