<?php

namespace Transaction\Application\StoreUser;

use Transaction\Application\Contracts\InputBoundary as InputBoundaryInterface;
use Transaction\Application\Contracts\OutputBoundary as OutputBoundaryInterface;
use Transaction\Application\Contracts\ServiceInterface;
use Transaction\Application\Login\InputBoundary as LoginInputBoundary;
use Transaction\Application\Login\Service as LoginService;
use Transaction\Domain\Contracts\UserRepository;
use Transaction\Domain\Entities\User as UserEntity;

class Service implements ServiceInterface
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly LoginService $loginService
    ) {
    }

    public function handle(InputBoundaryInterface $input): OutputBoundaryInterface
    {
        $user = $this->getNewUser($input);
        $user = $this->repository->store($user);

        $input = new LoginInputBoundary(
            email: $user->getEmail(),
            password: $input->getPassword()
        );

        return $this->loginService->handle($input);
    }

    private function getNewUser(InputBoundaryInterface $input): UserEntity
    {
        return UserEntity::newUser(
            name: $input->getName(),
            email: $input->getEmail(),
            registrationNumber: $input->getRegistrationNumber(),
            type: $input->getType(),
            password: $input->getPassword()
        );
    }
}
