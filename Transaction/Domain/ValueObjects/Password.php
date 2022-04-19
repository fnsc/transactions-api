<?php

namespace Transaction\Domain\ValueObjects;

use Stringable;

class Password implements Stringable
{
    public function __construct(private readonly string $password)
    {
    }

    public function __toString(): string
    {
        if (empty($this->password)) {
            return $this->password;
        }

        return password_hash($this->password, PASSWORD_ARGON2ID);
    }

    public function getPlainPassword(): string
    {
        return $this->password;
    }
}
