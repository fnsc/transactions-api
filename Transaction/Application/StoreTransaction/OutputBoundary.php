<?php

namespace Transaction\Application\StoreTransaction;

use Transaction\Application\Contracts\OutputBoundary as OutputBoundaryInterface;
use Transaction\Domain\Entities\Transaction;

class OutputBoundary implements OutputBoundaryInterface
{
    public function __construct(
        private readonly Transaction $transaction
    ) {
    }

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}
