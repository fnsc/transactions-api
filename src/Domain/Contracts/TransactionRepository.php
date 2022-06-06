<?php

namespace Transaction\Domain\Contracts;

use Transaction\Domain\Entities\Transaction;

interface TransactionRepository
{
    public function store(Transaction $transaction): Transaction;

    public function transfer(Transaction $transaction): Transaction;
}
