<?php

namespace Transaction\Domain\Contracts;

interface PasswordHasher
{
    public static function make(string $password): string;
}
