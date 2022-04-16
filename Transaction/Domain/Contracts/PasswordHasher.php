<?php

namespace Transaction\Domain\Contracts;

interface PasswordHasher
{
    public function make(string $password): string;
}
