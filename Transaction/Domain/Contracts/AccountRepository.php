<?php

namespace Transaction\Domain\Contracts;

use Transaction\Domain\Entities\Account;
use Transaction\Domain\Entities\User;

interface AccountRepository
{
    public function find(Account $account): ?Account;

    public function findByUser(User $user): Account;

    public function store(User $user): Account;

    public function update(Account $account): void;
}
