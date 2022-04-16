<?php

namespace Transaction\Application\Store;

use Transaction\Application\Contracts\InputBoundary as InputBoundaryInterface;

class InputBoundary implements InputBoundaryInterface
{
    public function __construct(
        private readonly int $payeeId,
        private readonly int $payerId,
        private readonly float $amount
    ) {
    }

    /**
     * @return int
     */
    public function getPayeeId(): int
    {
        return $this->payeeId;
    }

    /**
     * @return int
     */
    public function getPayerId(): int
    {
        return $this->payerId;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
}
