<?php

namespace Transaction\Infra\Adapters;

use Tests\TestCase;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Repositories\User as UserRepository;

class LoginAdapterTest extends TestCase
{
    public function testShouldGetSuccessWhenAttemptingTheLogin(): void
    {
        // Set
        $userRepository = $this->createMock(UserRepository::class);
        $loginAdapter = new LoginAdapter($userRepository);
        $user = UserEntity::newUser(
            email: 'random@email.com',
            password: 'secret'
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

    public function testShouldFailWhenEmailIsNotValid(): void
    {
        // Set
        $userRepository = $this->createMock(UserRepository::class);
        $loginAdapter = new LoginAdapter($userRepository);
        $user = UserEntity::newUser(
            email: 'random@email.com',
            password: 'secret'
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

    public function testShouldFailWhenPasswordIsNotValid(): void
    {
        // Set
        $userRepository = $this->createMock(UserRepository::class);
        $loginAdapter = new LoginAdapter($userRepository);
        $user = UserEntity::newUser(
            email: 'random@email.com',
            password: 'secret'
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
