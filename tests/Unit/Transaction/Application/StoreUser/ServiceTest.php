<?php

namespace Transaction\Application\StoreUser;

use Tests\TestCase;
use Transaction\Application\Login\InputBoundary as LoginInputBoundary;
use Transaction\Application\Login\OutputBoundary;
use Transaction\Application\Login\Service as LoginService;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Repositories\User as UserRepository;

class ServiceTest extends TestCase
{
    public function test_should_handle_with_the_new_user_data(): void
    {
        // Set
        $repository = $this->createMock(UserRepository::class);
        $loginService = $this->createMock(LoginService::class);
        $service = new Service($repository, $loginService);

        $input = new InputBoundary('random name', 'random@email.com', '12345678909', 'regular', 'secret');
        $user = UserEntity::newUser(
            name: 'random name',
            email: 'random@email.com',
            registrationNumber: '12345678909',
            type: 'regular',
            password: 'secret'
        );
        $loginInput = new LoginInputBoundary('random@email.com', 'secret');
        $output = new OutputBoundary($user);

        // Expectations
        $repository->expects($this->once())
            ->method('store')
            ->with($user)
            ->willReturn($user);

        $loginService->expects($this->once())
            ->method('handle')
            ->with($loginInput)
            ->willReturn($output);

        // Actions
        $result = $service->handle($input);

        // Assertions
        $this->assertInstanceOf(OutputBoundary::class, $result);
        $this->assertInstanceOf(UserEntity::class, $result->getUser());
    }
}
