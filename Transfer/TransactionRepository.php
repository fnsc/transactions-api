<?php

namespace Transfer;

use Illuminate\Support\Facades\DB;
use Transfer\Authorization\Service as AuthorizationService;
use Transfer\Store\Transfer;
use User\User;

class TransactionRepository
{
    use GenerateObjectId;

    const MONEY_SUBTRACT = 'subtract';
    const MONEY_ADD = 'add';

    private Transaction $transaction;
    private AccountRepository $accountRepository;
    private AuthorizationService $authorizationService;

    public function __construct(
        Transaction $transaction,
        AccountRepository $accountRepository,
        AuthorizationService $authorizationService
    ) {
        $this->transaction = $transaction;
        $this->accountRepository = $accountRepository;
        $this->authorizationService = $authorizationService;
    }

    public function store(Transfer $transfer): Transaction
    {
        $data = array_merge($transfer->toArray(), ['number' => $this->getNumber()]);

        return $this->transaction->create($data);
    }

    public function transfer(Transfer $transfer, User $payer, User $payee): Transaction
    {
        return DB::transaction(function () use ($transfer, $payer, $payee) {
            $this->updateAccount($payer->getAttribute('account'), $transfer, self::MONEY_SUBTRACT);
            $this->updateAccount($payee->getAttribute('account'), $transfer, self::MONEY_ADD);

            $transaction = $this->store($transfer);

            if (!$this->authorizationService->handle($transaction)) {
                throw FraudException::authorizationDeclined();
            }

            return $transaction;
        });
    }

    private function updateAccount(Account $account, Transfer $transfer, string $method): void
    {
        $data['amount'] = $this->getNewAmount($account, $transfer, $method);

        $this->accountRepository->update($data, $account->getAttribute('id'));
    }

    private function getNewAmount(Account $account, Transfer $transfer, string $method): int
    {
        return (int) $account->getAmount()
            ->{$method}($transfer->getAmount())
            ->getAmount();
    }
}
