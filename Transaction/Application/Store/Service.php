<?php

namespace Transaction\Application\Store;

use Money\MoneyFormatter;
use Transaction\Application\Contracts\AuthenticatedUserAdapter;
use Transaction\Application\Contracts\EventDispatcher;
use Transaction\Application\Contracts\InputBoundary as InputBoundaryInterface;
use Transaction\Application\Contracts\OutputBoundary as OutputBoundaryInterface;
use Transaction\Application\Contracts\ServiceInterface;
use Transaction\Application\Events\TransferProcessed;
use Transaction\Domain\Contracts\TransactionRepository;
use Transaction\Domain\Contracts\UserRepository as UserRepositoryInterface;
use Transaction\Domain\Entities\Transaction;
use Transaction\Domain\Entities\User;
use Transaction\TransferException;

class Service implements ServiceInterface
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly AuthenticatedUserAdapter $authenticatedUser,
        private readonly EventDispatcher $eventDispatcher
    ) {
    }

    public function handle(InputBoundaryInterface $input): OutputBoundaryInterface
    {
        $transaction = $this->getTransaction($input);
        $authenticatedUser = $this->authenticatedUser->getAuthenticatedUser();

        if ($transaction->getPayer()->getId() !== $authenticatedUser->getId()) {
            throw FraudException::payerIdisDifferent();
        }

        if (!$payer = $this->userRepository->find($transaction->getPayer()->getId())) {
            throw TransferException::payerNotFound();
        }

        if ($transaction->getAmount() > $payer->getAccount()->getAmount()) {
            throw TransferException::notSufficientAmount();
        }

        if (!$payee = $this->userRepository->find($transaction->getPayee()->getId())) {
            throw TransferException::payeeNotFound();
        }

        $transaction->setPayee($payee)->setPayer($payer);
        $transaction = $this->transactionRepository->transfer($transaction);

        $this->eventDispatcher->dispatch(new TransferProcessed($transaction, app(MoneyFormatter::class)));

        return new OutputBoundary('You did it!!!', compact('transaction'));
    }

    private function getTransaction(InputBoundaryInterface $input): Transaction
    {
        $amount = $this->getFormattedAmount($input);

        return new Transaction(
            User::newUser($input->getPayeeId()),
            User::newUser($input->getPayerId()),
            $amount
        );
    }

    private function getFormattedAmount(InputBoundaryInterface $input): int
    {
        return (int)number_format($input->getAmount(), 2, '', '');
    }
}
