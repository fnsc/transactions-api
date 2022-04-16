<?php

namespace Transaction\Application\Login;

use Transaction\Application\Contracts\InputBoundary as InputBoundaryInterface;
use Transaction\Application\Contracts\LoginAdapter;
use Transaction\Application\Contracts\OutputBoundary as OutputBoundaryInterface;
use Transaction\Application\Contracts\ServiceInterface;
use Transaction\Domain\Contracts\UserRepository;
use Transaction\Domain\Entities\User as UserEntity;
use User\LoginException;

class Service implements ServiceInterface
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly LoginAdapter $login
    ) {
    }

    public function handle(InputBoundaryInterface $input): OutputBoundaryInterface
    {
        $user = $this->getNewUser($input);

        if (!$this->login->attempt($user)) {
            throw LoginException::invalidData();
        }

        if (!$user = $this->getUser($input->getEmail())) {
            throw LoginException::userNotFound();
        }

        $user = $this->repository->authenticate($user);

        return new OutputBoundary($user);
    }

    private function getUser(string $email): ?UserEntity
    {
        return $this->repository->findByEmail($email);
    }

    private function getNewUser(InputBoundaryInterface $input): UserEntity
    {
        return UserEntity::newUser(
            email: $input->getEmail(),
            password: $input->getPassword()
        );
    }
}
