<?php

namespace Transaction\Domain\Entities;

use Money\Money;

class Account
{
    public function __construct(
        private int $amount = 0,
        private readonly int $userId = 0,
        private readonly string $number = '',
        private readonly int $id = 0,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getAmount(): Money
    {
        return Money::BRL($this->amount);
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }
}
