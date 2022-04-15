<?php

namespace Transaction\Infra\Transformers;

use Transaction\Domain\Entities\Transaction as TransactionEntity;

class Transaction
{
    public function transform(TransactionEntity $transaction): array
    {
        return [
            'payer' => $transaction->getPayer()->getName(),
            'payee' => $transaction->getPayee()->getName(),
            'amount' => $transaction->getAmount()->getAmount(),
        ];
    }
}
