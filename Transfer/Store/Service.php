<?php

namespace Transfer\Store;

use Money\MoneyFormatter;
use Transfer\AuthenticatedUser;
use Transfer\Events\TransferProcessed;
use Transfer\FraudException;
use Transfer\TransactionRepository;
use Transfer\TransferException;
use User\Repository;

class Service
{
    private TransactionRepository $transactionRepository;
    private Repository $userRepository;

    public function __construct(TransactionRepository $transactionRepository, Repository $userRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
    }

    public function handle(Transfer $transfer, AuthenticatedUser $user): array
    {
        if ($transfer->getPayerId() !== $user->getId()) {
            throw FraudException::payerIdisDifferent();
        }

        if (!$payer = $this->userRepository->find($transfer->getPayerId())) {
            throw TransferException::payerNotFound();
        }

        if ($transfer->getAmount() > $payer->account->getAmount()) {
            throw TransferException::notSufficientAmount();
        }

        if (!$payee = $this->userRepository->find($transfer->getPayeeId())) {
            throw TransferException::payeeNotFound();
        }

        $transaction = $this->transactionRepository->transfer($transfer, $payer, $payee);

        event(new TransferProcessed($transaction, app(MoneyFormatter::class)));

        return [
            'message' => 'You did it!!!',
            'data' => [],
        ];
    }
}
