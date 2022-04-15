<?php

namespace Transaction\Infra\Transformers;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Transaction\Domain\Entities\User as UserEntity;

class UserTest extends TestCase
{
    public function test_should_transform_the_user(): void
    {
        // Set
        $user = m::mock(UserEntity::class);
        $transformer = new User();

        // Expectations
        $user->expects()
            ->getName()
            ->andReturn('Payee Name');

        $user->expects()
            ->getToken()
            ->andReturn('auth token');

        // Actions
        $result = $transformer->transform($user);

        // Assertions
        $this->assertSame([
            'name' => 'Payee Name',
            'auth' => [
                'token' => 'auth token',
            ],
        ], $result);
    }
}
