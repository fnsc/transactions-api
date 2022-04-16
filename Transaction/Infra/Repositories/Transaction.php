<?php

namespace Transaction\Infra\Repositories;

use Illuminate\Support\Facades\DB;
use Transaction\Application\Authorization\Service as AuthorizationService;
use Transaction\Application\Store\FraudException;
use Transaction\Domain\Contracts\AccountRepository;
use Transaction\Domain\Contracts\TransactionRepository as TransactionRepositoryInterface;
use Transaction\Domain\Entities\Transaction as TransactionEntity;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\GenerateObjectId;
use Transaction\Domain\Entities\Account as AccountEntity;
use Transaction\Infra\Eloquent\Transaction as TransactionModel;
use Transaction\Infra\Eloquent\User as UserModel;

class Transaction implements TransactionRepositoryInterface
{
    use GenerateObjectId;

    private const MONEY_SUBTRACT = 'subtract';
    private const MONEY_ADD = 'add';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly AuthorizationService $authorizationService
    ) {
    }

    public function store(TransactionEntity $transaction): TransactionEntity
    {
        $transactionModel = $this->getModel();
        $data = array_merge($transaction->toArray(), ['number' => $this->getNumber()]);

        $transactionModel = $transactionModel->create($data);

        return $this->getTransactionEntity($transactionModel);
    }

    public function transfer(TransactionEntity $transaction): TransactionEntity
    {
        return DB::transaction(function () use ($transaction) {
            $this->updateAccount($transaction->getPayer()->getAccount(), $transaction, self::MONEY_SUBTRACT);
            $this->updateAccount($transaction->getPayee()->getAccount(), $transaction, self::MONEY_ADD);

            $transaction = $this->store($transaction);

            if (!$this->authorizationService->handle($transaction)) {
                throw FraudException::authorizationDeclined();
            }

            return $transaction;
        });
    }

    private function updateAccount(AccountEntity $account, TransactionEntity $transfer, string $method): void
    {
        $account->setAmount($this->getNewAmount($account, $transfer, $method));

        $this->accountRepository->update($account);
    }

    private function getNewAmount(AccountEntity $account, TransactionEntity $transfer, string $method): int
    {
        return (int) $account->getAmount()
            ->{$method}($transfer->getAmount())
            ->getAmount();
    }

    private function getModel(): TransactionModel
    {
        return new TransactionModel();
    }

    private function getTransactionEntity(TransactionModel $transactionModel): TransactionEntity
    {
        $payer = $transactionModel->payer;
        $payee = $transactionModel->payee;

        return new TransactionEntity(
            $this->getNewUser($payee),
            $this->getNewUser($payer),
            $transactionModel->amount,
            $transactionModel->number
        );
    }

    private function getNewUser(UserModel $user): UserEntity
    {
        return UserEntity::newUser(
            $user->id,
            $user->name,
            $user->email,
            $user->registration_number,
            $user->type,
        );
    }
}
