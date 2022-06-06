<?php

namespace Transaction\Application\Events;

use Transaction\Application\Contracts\EventInterface;
use Transaction\Domain\Entities\Transaction;

class TransferProcessed implements EventInterface
{
    public function __construct(
        private readonly Transaction $transaction,
    ) {
    }

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}
