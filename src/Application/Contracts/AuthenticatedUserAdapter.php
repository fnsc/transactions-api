<?php

namespace Transaction\Application\Contracts;

use Transaction\Domain\Entities\User;

interface AuthenticatedUserAdapter
{
    public function getAuthenticatedUser(): User;
}
