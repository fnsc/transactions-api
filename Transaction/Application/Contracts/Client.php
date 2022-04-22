<?php

namespace Transaction\Application\Contracts;

use Psr\Http\Message\ResponseInterface;
use Transaction\Domain\Entities\Transaction;

interface Client
{
    public function send(Transaction $transaction): ResponseInterface;
}
