<?php

namespace Transaction\Application\Contracts;

use Transaction\Domain\Entities\User;

interface LoginAdapter
{
    public function attempt(User $user): bool;
}
