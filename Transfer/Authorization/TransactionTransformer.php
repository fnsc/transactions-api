<?php

namespace Transfer\Authorization;

use Money\Money;
use Money\MoneyFormatter;
use Transfer\Transaction;

class TransactionTransformer
{
    private MoneyFormatter $moneyParser;

    public function __construct(MoneyFormatter $moneyParser)
    {
        $this->moneyParser = $moneyParser;
    }

    public function transform(Transaction $transaction): array
    {
        $amount = $transaction->getAmount();
        $payerFiscalDoc = $transaction->getAttribute('payer')
            ->getAttribute('fiscal_doc');
        $payeeFiscalDoc = $transaction->getAttribute('payee')
            ->getAttribute('fiscal_doc');

        return [
            'number' => $transaction->getAttribute('number'),
            'payerFiscalDoc' => $payerFiscalDoc,
            'payeeFiscalDoc' => $payeeFiscalDoc,
            'amount' => $this->getFormattedAmount($amount),
        ];
    }

    private function getFormattedAmount(Money $amount): string
    {
        return $this->moneyParser->format($amount);
    }
}
