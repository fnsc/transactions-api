<?php

namespace Tests\Unit\Transaction\Application\Login;

use Tests\TestCase;
use Transaction\Application\Contracts\LoginAdapter;
use Transaction\Application\Contracts\OutputBoundary;
use Transaction\Application\Exceptions\LoginException;
use Transaction\Application\Login\InputBoundary;
use Transaction\Application\Login\Service;
use Transaction\Domain\Contracts\UserRepository;
use Transaction\Domain\Entities\User;

class ServiceTest extends TestCase
{
    public function test_should_handle_the_login(): void
    {
        // Set
        $userRepository = $this->createMock(UserRepository::class);
        $loginAdapter = $this->createMock(LoginAdapter::class);
        $service = new Service($userRepository, $loginAdapter);

        $input = new InputBoundary('regular@email.com', 'secret');
        $user = User::newUser(email: 'regular@email.com', password: 'secret');

        // Expectations
        $userRepository->expects($this->once())
            ->method('findByEmail')
            ->with($user->getEmail())
            ->willReturn($user);

        $loginAdapter->expects($this->once())
            ->method('attempt')
            ->with($user)
            ->willReturn(true);

        // Actions
        $result = $service->handle($input);

        // Assertions
        $this->assertInstanceOf(OutputBoundary::class, $result);
        $this->assertInstanceOf(User::class, $result->getUser());
    }

    public function test_should_throw_an_exception_when_login_failed(): void
    {
        // Set
        $userRepository = $this->createMock(UserRepository::class);
        $loginAdapter = $this->createMock(LoginAdapter::class);
        $service = new Service($userRepository, $loginAdapter);

        $input = new InputBoundary('regular@email.com', 'secret');
        $user = User::newUser(email: 'regular@email.com', password: 'secret');

        // Expectations
        $userRepository->expects($this->once())
            ->method('findByEmail')
            ->with($user->getEmail())
            ->willReturn($user);

        $loginAdapter->expects($this->once())
            ->method('attempt')
            ->with($user)
            ->willReturn(false);

        $this->expectException(LoginException::class);
        $this->expectExceptionMessage('The given data is invalid.');

        // Actions
        $service->handle($input);
    }

    public function test_should_throw_an_exception_when_user_does_not_exist(): void
    {
        // Set
        $userRepository = $this->createMock(UserRepository::class);
        $loginAdapter = $this->createMock(LoginAdapter::class);
        $service = new Service($userRepository, $loginAdapter);

        $input = new InputBoundary('regular@email.com', 'secret');
        $user = User::newUser(email: 'regular@email.com', password: 'secret');

        // Expectations
        $userRepository->expects($this->once())
            ->method('findByEmail')
            ->with($user->getEmail())
            ->willReturn(null);

        $this->expectException(LoginException::class);
        $this->expectExceptionMessage('User not found.');

        // Actions
        $service->handle($input);
    }
}
