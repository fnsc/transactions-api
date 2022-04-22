<?php

namespace Transfer\Store;

use PHPUnit\Framework\TestCase;

class TransferTest extends TestCase
{
    public function test_should_test_transfer_object(): void
    {
        // Set
        $data = [
            'payee_id' => 2,
            'payer_id' => 1,
            'amount' => '100,00',
        ];
        $transfer = new Transfer($data);

        // Actions
        $payeeId = $transfer->getPayeeId();
        $payerId = $transfer->getPayerId();
        $amount = $transfer->getAmount();
        $toArray = $transfer->toArray();

        // Assertions
        $this->assertSame(2, $payeeId);
        $this->assertSame(1, $payerId);
        $this->assertSame('10000', $amount->getAmount());
        $this->assertSame(
            [
                'payer_id' => 1,
                'payee_id' => 2,
                'amount' => 10000,
            ],
            $toArray
        );
    }
}
