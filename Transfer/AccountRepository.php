<?php

namespace Transfer;

use User\User;

class AccountRepository
{
    use GenerateObjectId;

    private Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function find(int $accountId): ?Account
    {
        return $this->account->where('id', $accountId)->first();
    }

    public function store(User $user): Account
    {
        return $this->account->create([
            'number' => $this->getNumber(),
            'user_id' => $user->getAttribute('id'),
            'amount' => 0,
        ]);
    }

    public function update(array $data, int $accountId): void
    {
        if (!$account = $this->find($accountId)) {
            throw TransferException::accountNotFound();
        }

        $this->removeNumberField($data);
        $account->update($data);
    }

    private function removeNumberField(array &$data): void
    {
        unset($data['number']);
    }
}
