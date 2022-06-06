<?php

namespace Transaction\Application\StoreUser;

use Transaction\Application\Contracts\InputBoundary as InputBoundaryInterface;

class InputBoundary implements InputBoundaryInterface
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly string $registrationNumber,
        private readonly string $type,
        private readonly string $password
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
