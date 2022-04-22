<?php

namespace Transaction\Domain\ValueObjects;

use Stringable;

class Email implements Stringable
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
