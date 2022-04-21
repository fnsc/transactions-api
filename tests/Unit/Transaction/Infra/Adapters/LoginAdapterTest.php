<?php

namespace Transaction\Infra\Adapters;

use Tests\TestCase;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Repositories\User as UserRepository;

class LoginAdapterTest extends TestCase
{
    public function test_should_get_success_when_attempting_the_login(): void
    {
        // Set
        $userRepository = $this->createMock(UserRepository::class);
        $loginAdapter = new LoginAdapter($userRepository);
        $user = UserEntity::newUser(
            email: 'random@email.com',
            password: 'secret',
        );

        $userDatabase = UserEntity::newUser(
            email: 'random@email.com',
            password: password_hash('secret', PASSWORD_ARGON2ID),
        );

        // Expectations
        $userRepository->expects($this->once())
            ->method('getLoginCredentials')
            ->with($user->getEmail())
            ->willReturn($userDatabase);

        // Actions
        $result = $loginAdapter->attempt($user);

        // Assertions
        $this->assertTrue($result);
    }

    public function test_should_fail_when_email_is_not_valid(): void
    {
        // Set
        $userRepository = $this->createMock(UserRepository::class);
        $loginAdapter = new LoginAdapter($userRepository);
        $user = UserEntity::newUser(
            email: 'random@email.com',
            password: 'secret',
        );

        $userDatabase = UserEntity::newUser(
            email: 'random@email.com.br',
            password: password_hash('secret', PASSWORD_ARGON2ID)
        );

        // Expectations
        $userRepository->expects($this->once())
            ->method('getLoginCredentials')
            ->with($user->getEmail())
            ->willReturn($userDatabase);

        // Actions
        $result = $loginAdapter->attempt($user);

        // Assertions
        $this->assertFalse($result);
    }

    public function test_should_fail_when_password_is_not_valid(): void
    {
        // Set
        $userRepository = $this->createMock(UserRepository::class);
        $loginAdapter = new LoginAdapter($userRepository);
        $user = UserEntity::newUser(
            email: 'random@email.com',
            password: 'secret',
        );

        $userDatabase = UserEntity::newUser(
            email: 'random@email.com.br',
            password: password_hash('secrets', PASSWORD_ARGON2ID)
        );

        // Expectations
        $userRepository->expects($this->once())
            ->method('getLoginCredentials')
            ->with($user->getEmail())
            ->willReturn($userDatabase);

        // Actions
        $result = $loginAdapter->attempt($user);

        // Assertions
        $this->assertFalse($result);
    }
}
