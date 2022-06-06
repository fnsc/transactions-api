<?php

namespace Transaction\Infra\Repositories;

use Transaction\Application\Exceptions\UserException;
use Transaction\Domain\Contracts\UserRepository;
use Transaction\Domain\Entities\Account as AccountEntity;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Domain\UserType;
use Transaction\Domain\ValueObjects\Email;
use Transaction\Infra\Eloquent\User as UserModel;

class User implements UserRepository
{
    public function __construct(private readonly Account $accountRepository)
    {
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

        if (
            !$this->isUnique(
                $user->getRegistrationNumber(),
                'registration_number'
            )
        ) {
            throw UserException::fiscalDocAlreadyExists();
        }

        $userModel = $this->getModel();

        $user = $userModel->create([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => (string) $user->getPassword(),
            'registration_number' => $user->getRegistrationNumber(),
            'type' => $user->getType(),
        ]);

        if (!$user) {
            throw UserException::failedStoring();
        }

        $user = $this->getNewUser($user);
        $account = $this->accountRepository->store($user);
        $user->setAccount($account);

        return $user;
    }

    public function findByEmail(Email $email): ?UserEntity
    {
        $userModel = $this->getModel();
        $userModel = $userModel->where('email', (string) $email)->first();

        return $this->getNewUser($userModel);
    }

    public function getLoginCredentials(Email $email): ?UserEntity
    {
        $userModel = $this->getModel();
        $userModel = $userModel->where('email', (string) $email)->first();

        return UserEntity::newUser(
            email: $userModel->getAttribute('email'),
            password: $userModel->getAttribute('password'),
        );
    }

    public function authenticate(UserEntity $user): UserEntity
    {
        $userModel = $this->getModel();
        $userModel = $userModel->whereId($user->getId())->first();
        $userModel->tokens()->delete();

        return UserEntity::newUser(
            id: $userModel->getAttribute('id'),
            name: $userModel->getAttribute('name'),
            email: $userModel->getAttribute('email'),
            registrationNumber: $userModel->getAttribute(
                'registration_number'
            ),
            type: $userModel->getAttribute('type'),
            token: $userModel->createToken('auth')->plainTextToken
        );
    }

    private function isUnique(mixed $value, string $field): bool
    {
        $userModel = $this->getModel();

        return !$userModel->where($field, $value)->first();
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

    private function getNewUser(UserModel $userModel): UserEntity
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
