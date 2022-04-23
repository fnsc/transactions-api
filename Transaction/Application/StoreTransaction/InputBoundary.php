<?php

namespace Transaction\Application\StoreTransaction;

use Transaction\Application\Contracts\InputBoundary as InputBoundaryInterface;

class InputBoundary implements InputBoundaryInterface
{
    public function __construct(
        private readonly int $payeeId,
        private readonly int $payerId,
        private readonly float $amount
    ) {
    }

    public function getPayeeId(): int
    {
        return $this->payeeId;
    }

    public function getPayerId(): int
    {
        return $this->payerId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
