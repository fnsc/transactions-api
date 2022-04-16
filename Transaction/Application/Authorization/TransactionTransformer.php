<?php

namespace Transaction\Application\Authorization;

use Money\Money;
use Money\MoneyFormatter;
use Transaction\Domain\Entities\Transaction;

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
        $payerRegistrationNumber = $transaction->getPayer()->getRegistrationNumber();
        $payeeRegistrationNumber = $transaction->getPayee()->getRegistrationNumber();

        return [
            'number' => $transaction->getNumber(),
            'payerRegistrationNumber' => $payerRegistrationNumber,
            'payeeRegistrationNumber' => $payeeRegistrationNumber,
            'amount' => $this->getFormattedAmount($amount),
        ];
    }

    private function getFormattedAmount(Money $amount): string
    {
        return $this->moneyParser->format($amount);
    }
}
