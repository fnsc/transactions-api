<?php

namespace User;

use User\Store\User as UserValueObject;

class Repository
{
    private const USER_TYPE = ['regular', 'seller'];

    private User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function store(UserValueObject $user): User
    {
        if (!in_array($user->getType(), self::USER_TYPE, true)) {
            throw UserException::invalidUserType();
        }

        if (!$this->isUnique($user->getEmail(), 'email')) {
            throw UserException::emailAlreadyExists();
        }

        if (!$this->isUnique($user->getFiscalDoc(), 'fiscal_doc')) {
            throw UserException::fiscalDocAlreadyExists();
        }

        $user = $this->model->create($user->toArray());

        if (!$user) {
            throw UserException::failedStoring();
        }

        return $user;
    }

    private function isUnique(mixed $value, string $field): bool
    {
        return !(bool) $this->model->where($field, $value)->first();
    }
}
