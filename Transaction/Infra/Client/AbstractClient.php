<?php

namespace Transaction\Infra\Client;

use DateTime;
use Money\MoneyFormatter;
use Transaction\Domain\Entities\Transaction;

abstract class AbstractClient
{
    public function __construct(private readonly MoneyFormatter $moneyFormatter)
    {
    }

    protected function getOptions(Transaction $transaction): array
    {
        $options = [];
        $options['header'] = $this->getHeader();
        $options['body'] = json_encode([
            'payer' => $transaction->getPayer()->getName(),
            'payee' => $transaction->getPayee()->getName(),
            'amount' => $this->moneyFormatter->format($transaction->getAmount()),
            'created_at' => (new DateTime())->format(DATE_ATOM),
        ]);

        return $options;
    }

    private function getHeader(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
