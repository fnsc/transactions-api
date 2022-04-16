<?php

namespace Transaction\Infra\Adapters;

use Illuminate\Support\Facades\Hash;
use Transaction\Domain\Contracts\PasswordHasher as PasswordHasherInterface;

class PasswordHasher implements PasswordHasherInterface
{
    public function make(string $password): string
    {
        return Hash::make($password);
    }
}
