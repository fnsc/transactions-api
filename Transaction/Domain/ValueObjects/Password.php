<?php

namespace Transaction\Domain\ValueObjects;

use Stringable;
use Transaction\Domain\Contracts\PasswordHasher;

class Password implements Stringable
{
    private string $password;
    private PasswordHasher $hasher;

    public function __construct(string $password, PasswordHasher $hasher)
    {
        $this->password = $password;
        $this->hasher = $hasher;
    }

    public function __toString(): string
    {
        if (empty($this->password)) {
            return $this->password;
        }

        return $this->hasher->make($this->password);
    }

    public function getPlainPassword(): string
    {
        return $this->password;
    }
}
