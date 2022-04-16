<?php

namespace Transaction\Application\Events;

use DateTime;
use Illuminate\Support\Str;
use Money\Money;
use Money\MoneyFormatter;
use Transaction\Application\Contracts\EventInterface;
use Transaction\Domain\Entities\Transaction;

class TransferProcessed implements EventInterface
{
    private string $number;
    private string $payee;
    private string $payer;
    private Money $amount;
    private DateTime $created_at;
    private MoneyFormatter $moneyFormatter;

    public function __construct(Transaction $transaction, MoneyFormatter $moneyFormatter)
    {
        $this->number = $transaction->getNumber();
        $this->payee = $transaction->getPayee()->getName();
        $this->payer = $transaction->getPayer()->getName();
        $this->amount = $transaction->getAmount();
        $this->created_at = new DateTime();

        $this->moneyFormatter = $moneyFormatter;
    }

    public function getAttributes(): array
    {
        return [
            'number' => $this->getNumber(),
            'payee' => $this->getPayee(),
            'payer' => $this->getPayer(),
            'amount' => $this->getAmount(),
            'created_at' => $this->getCreatedAt(),
        ];
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getPayee(): string
    {
        return Str::headline($this->payee);
    }

    public function getPayer(): string
    {
        return Str::headline($this->payer);
    }

    public function getAmount(): string
    {
        return $this->moneyFormatter->format($this->amount);
    }

    public function getCreatedAt(): string
    {
        return $this->created_at->format(DATE_ISO8601);
    }
}
