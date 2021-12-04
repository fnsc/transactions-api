<?php

namespace Transfer\Store;

use Money\Money;

class Transfer
{
    private int $payeeId;
    private int $payerId;
    private int $amount;

    public function __construct(array $data)
    {
        $this->payeeId = (int) $data['payee_id'];
        $this->payerId = (int) $data['payer_id'];
        $this->amount = (int) number_format((float) $data['amount'], 2, '', '');
    }

    public function getPayeeId(): int
    {
        return $this->payeeId;
    }

    public function getPayerId(): int
    {
        return $this->payerId;
    }

    public function getAmount(): Money
    {
        return Money::BRL($this->amount);
    }

    public function toArray(): array
    {
        return [
            'payer_id' => $this->getPayerId(),
            'payee_id' => $this->getPayeeId(),
            'amount' => (int) $this->getAmount()->getAmount(),
        ];
    }
}
