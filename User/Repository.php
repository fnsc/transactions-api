<?php

namespace User;

use Transfer\AccountRepository;
use User\Store\User as UserValueObject;

class Repository
{
    private const USER_TYPES = [EnumUserType::REGULAR, EnumUserType::SELLER];

    private User $user;
    private AccountRepository $accountRepository;

    public function __construct(User $user, AccountRepository $accountRepository)
    {
        $this->user = $user;
        $this->accountRepository = $accountRepository;
    }

    public function find(int $id): ?User
    {
        return $this->user->where('id', $id)->first();
    }

    public function store(UserValueObject $user): User
    {
        if (!in_array($user->getType(), self::USER_TYPES, true)) {
            throw UserException::invalidUserType();
        }

        if (!$this->isUnique($user->getEmail(), 'email')) {
            throw UserException::emailAlreadyExists();
        }

        if (!$this->isUnique($user->getRegistrationNumber(), 'registration_number')) {
            throw UserException::fiscalDocAlreadyExists();
        }

        $user = $this->user->create($user->toArray());

        if (!$user) {
            throw UserException::failedStoring();
        }

        $this->accountRepository->store($user);

        return $user;
    }

    public function findByEmail(mixed $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }

    private function isUnique(mixed $value, string $field): bool
    {
        return !(bool) $this->user->where($field, $value)->first();
    }
}
