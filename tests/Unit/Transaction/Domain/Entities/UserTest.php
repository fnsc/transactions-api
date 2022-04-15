<?php

namespace Transaction\Domain\Entities;

use PHPUnit\Framework\TestCase;
use Transaction\Domain\ValueObjects\Email;
use Transaction\Domain\ValueObjects\Password;

class UserTest extends TestCase
{
    public function test_should_get_an_user_instance(): void
    {
        // Actions
        $user = User::newUser(
            id: 1,
            name: 'Random User',
            email: 'random@email.com',
            registrationNumber: '12345678909',
            type: 'regular',
            password: 'secret',
            token: 'auth-token'
        );

        // Assertions
        $this->assertSame(1, $user->getId());
        $this->assertSame('Random User', $user->getName());
        $this->assertSame('random@email.com', (string) $user->getEmail());
        $this->assertInstanceOf(Email::class, $user->getEmail());
        $this->assertSame('12345678909', $user->getRegistrationNumber());
        $this->assertSame('regular', $user->getType());
        $this->assertSame('secret', $user->getPassword()->getPlainPassword());
        $this->assertInstanceOf(Password::class, $user->getPassword());
        $this->assertSame('auth-token', $user->getToken());
    }
}
