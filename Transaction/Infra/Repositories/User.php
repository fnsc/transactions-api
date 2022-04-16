<?php

namespace Transaction\Infra\Repositories;

use Transaction\Domain\Contracts\UserRepository;
use Transaction\Domain\Entities\Account as AccountEntity;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Domain\UserType;
use Transaction\Infra\Eloquent\User as UserModel;
use User\UserException;

class User implements UserRepository
{
    private Account $accountRepository;

    public function __construct(Account $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function find(int $id): ?UserEntity
    {
        $userModel = $this->getModel();

        if (!$userModel = $userModel->where('id', $id)->first()) {
            return null;
        }

        $user = $this->getNewUser($userModel);
        $account = $this->getAccount($userModel);
        $user->setAccount($account);

        return $user;
    }

    public function store(UserEntity $user): UserEntity
    {
        $userTypes = [UserType::REGULAR->value, UserType::SELLER->value];

        if (!in_array($user->getType(), $userTypes, true)) {
            throw UserException::invalidUserType();
        }

        if (!$this->isUnique($user->getEmail(), 'email')) {
            throw UserException::emailAlreadyExists();
        }

        if (!$this->isUnique($user->getRegistrationNumber(), 'registration_number')) {
            throw UserException::fiscalDocAlreadyExists();
        }

        $userModel = $this->getModel();

        $user = $userModel->create($user->toArray());

        if (!$user) {
            throw UserException::failedStoring();
        }

        $this->accountRepository->store($user);

        return $user;
    }

    public function findByEmail(mixed $email): ?UserEntity
    {
        $userModel = $this->getModel();

        return $userModel->where('email', $email)->first();
    }

    private function isUnique(mixed $value, string $field): bool
    {
        $userModel = $this->getModel();

        return !(bool) $userModel->where($field, $value)->first();
    }

    private function getModel(): UserModel
    {
        return new UserModel();
    }

    private function getAccount($userModel): AccountEntity
    {
        return new AccountEntity(
            $userModel->account->amount,
            $userModel->account->user_id,
            $userModel->account->number,
            $userModel->account->id
        );
    }

    private function getNewUser($userModel): UserEntity
    {
        return UserEntity::newUser(
            $userModel->getAttribute('id'),
            $userModel->getAttribute('name'),
            $userModel->getAttribute('email'),
            $userModel->getAttribute('registration_number'),
            $userModel->getAttribute('type'),
        );
    }
}
