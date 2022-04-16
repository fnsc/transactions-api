<?php

namespace Transaction\Infra\Repositories;

use Transaction\Domain\Contracts\AccountRepository as AccountRepositoryInterface;
use Transaction\Domain\Entities\Account as AccountEntity;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\GenerateObjectId;
use Transaction\Infra\Eloquent\Account as AccountModel;
use Transaction\TransferException;

class Account implements AccountRepositoryInterface
{
    use GenerateObjectId;

    public function find(int $accountId): ?AccountEntity
    {
        $accountModel = $this->getModel();

        return $accountModel->where('id', $accountId)->first();
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

        if (!$account = $accountModel->whereId($accountEntity->getId())) {
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
