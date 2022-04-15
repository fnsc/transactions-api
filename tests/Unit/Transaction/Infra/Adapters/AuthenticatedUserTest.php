<?php

namespace Transaction\Infra\Adapters;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Eloquent\User as UserModel;

class AuthenticatedUserTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    public function test_get_authenticated_user_instance(): void
    {
        $user = UserModel::create([
            'name' => 'Random Name',
            'email' => 'random@email.com',
            'registration_number' => '12345678909',
            'type' => 'regular',
            'password' => 'secret',
        ]);

        $this->actingAs($user);

        // Actions
        $authenticatedUser = new AuthenticatedUser();

        // Assertions
        $this->assertInstanceOf(UserEntity::class, $authenticatedUser->getAuthenticatedUser());
        $this->assertSame('Random Name', $authenticatedUser->getAuthenticatedUser()->getName());
    }
}
