<?php

namespace User;

use User\Store\User as UserValueObject;

class Repository
{
    private User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function store(UserValueObject $user): User
    {
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
