<?php

namespace Transaction\Application\Login;

use Transaction\Application\Contracts\InputBoundary as InputBoundaryInterface;
use Transaction\Application\Contracts\LoginAdapter;
use Transaction\Application\Contracts\OutputBoundary as OutputBoundaryInterface;
use Transaction\Application\Contracts\ServiceInterface;
use Transaction\Application\Exceptions\LoginException;
use Transaction\Domain\Contracts\UserRepository;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Domain\ValueObjects\Email;

class Service implements ServiceInterface
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly LoginAdapter $login
    ) {
    }

    public function handle(InputBoundaryInterface $input): OutputBoundaryInterface
    {
        $userInput = $this->getNewUser($input);

        if (!$userDatabase = $this->getUser($userInput->getEmail())) {
            throw LoginException::userNotFound();
        }

        if (!$this->login->attempt($userInput)) {
            throw LoginException::invalidData();
        }

        $user = $this->repository->authenticate($userDatabase);

        return new OutputBoundary($user);
    }

    private function getUser(Email $email): ?UserEntity
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
