<?php

namespace Transaction\Infra\Repositories;

use Transaction\Application\Exceptions\TransferException;
use Transaction\Domain\Contracts\AccountRepository as AccountRepositoryInterface;
use Transaction\Domain\Entities\Account as AccountEntity;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Eloquent\Account as AccountModel;
use Transaction\Infra\GenerateObjectId;

class Account implements AccountRepositoryInterface
{
    use GenerateObjectId;

    public function find(AccountEntity $account): ?AccountEntity
    {
        $accountModel = $this->getModel();

        if (!$accountModel = $accountModel->where('id', $account->getId())->first()) {
            return null;
        }

        return new AccountEntity(
            amount: $accountModel->getAttribute('amount'),
            userId: $accountModel->getAttribute('user_id'),
            number: $accountModel->getAttribute('number'),
            id: $accountModel->getAttribute('id')
        );
    }

    public function findByUser(UserEntity $user): AccountEntity
    {
        $accountModel = $this->getModel();

        return $accountModel->where('user_id', $user->getId())->first();
    }

    public function store(UserEntity $user): AccountEntity
    {
        $accountModel = $this->getModel();

        $accountModel = $accountModel->create([
            'number' => $this->getNumber(),
            'user_id' => $user->getId(),
            'amount' => 0,
        ]);

        return new AccountEntity(
            $accountModel->amount,
            $accountModel->user_id,
            $accountModel->number,
            $accountModel->id
        );
    }

    public function update(AccountEntity $accountEntity): void
    {
        $accountModel = $this->getModel();

        $account = $accountModel->whereId($accountEntity->getId())->first();

        if (!$account) {
            throw TransferException::accountNotFound();
        }

        $account->update([
            'amount' => $accountEntity->getAmount()->getAmount(),
        ]);
    }

    private function getModel(): AccountModel
    {
        return new AccountModel();
    }
}
