<?php

namespace Transaction\Infra\Presenters;

use Transaction\Domain\Entities\Transaction;

class TransactionTransformer
{
    public function transform(Transaction $transaction): array
    {
        return [
            'payer' => $transaction->getPayer()->getName(),
            'payee' => $transaction->getPayee()->getName(),
            'amount' => $transaction->getAmount()->getAmount(),
        ];
    }
}
