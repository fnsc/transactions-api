<?php

namespace Transaction\Application\Login;

use Transaction\Application\Contracts\OutputBoundary as OutputBoundaryInterface;
use Transaction\Domain\Entities\User;

class OutputBoundary implements OutputBoundaryInterface
{
    public function __construct(
        private readonly User $user
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
