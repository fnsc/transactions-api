<?php

namespace Transaction\Application\Contracts;

use Transaction\Domain\Entities\Transaction;

interface EventInterface
{
    public function getTransaction(): Transaction;
}
