<?php

namespace Transaction\Domain\Entities;

use Money\Money;

class Transaction
{
    public function __construct(
        private User $payee,
        private User $payer,
        private readonly int $amount,
        private readonly string $number = ''
    ) {
    }

    public function getPayee(): User
    {
        return $this->payee;
    }

    public function getPayer(): User
    {
        return $this->payer;
    }

    public function getAmount(): Money
    {
        return Money::BRL($this->amount);
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setPayee(User $payee): self
    {
        $this->payee = $payee;

        return $this;
    }

    public function setPayer(User $payer): self
    {
        $this->payer = $payer;

        return $this;
    }
}
