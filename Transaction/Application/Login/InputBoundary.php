<?php

namespace Transaction\Application\Login;

use Transaction\Application\Contracts\InputBoundary as InputBoundaryInterface;

class InputBoundary implements InputBoundaryInterface
{
    public function __construct(
        private readonly string $email,
        private readonly string $password
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
