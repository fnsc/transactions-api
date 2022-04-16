<?php

namespace Transaction\Domain\Contracts;

use Transaction\Domain\Entities\User;
use Transaction\Domain\ValueObjects\Email;

interface UserRepository
{
    public function find(int $id): ?User;

    public function store(User $user): User;

    public function findByEmail(Email $email): ?User;

    public function authenticate(User $user): User;
}
