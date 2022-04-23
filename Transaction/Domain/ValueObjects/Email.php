<?php

namespace Transaction\Domain\ValueObjects;

use Stringable;

class Email implements Stringable
{
    public function __construct(private readonly string $email)
    {
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
