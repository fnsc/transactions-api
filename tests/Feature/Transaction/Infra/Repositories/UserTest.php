<?php

namespace Tests\Feature\Transaction\Infra\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Transaction\Application\Exceptions\UserException;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Eloquent\User as UserModel;
use Transaction\Infra\Repositories\User as UserRepository;

use function app;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function testShouldStoreTheNewUser(): void
    {
        // Set
        $repository = app(UserRepository::class);
        $userEntity = UserEntity::newUser(
            name: 'some random name',
            email: 'some@email.com',
            registrationNumber: '12345678909',
            type: 'regular',
            password: 'secret',
        );

        // Actions
        $result = $repository->store($userEntity);

        // Assertions
        $this->assertDatabaseCount('users', 1);
        $this->assertInstanceOf(UserEntity::class, $result);
        $this->assertSame('some random name', $result->getName());
        $this->assertDatabaseHas('users', ['email' => 'some@email.com']);
    }

    public function testShouldThrowAnUserExceptionWhenTheEmailAlreadyExists(): void
    {
        // Set
        UserModel::create([
            'name' => 'some random name',
            'email' => 'some@email.com',
            'registration_number' => '12345678909',
            'type' => 'regular',
            'password' => 'secret',
        ]);

        $repository = app(UserRepository::class);
        $userEntity = UserEntity::newUser(
            name: 'some random name',
            email: 'some@email.com',
            registrationNumber: '12345678908',
            type: 'regular',
            password: 'secret',
        );

        // Expectations
        $this->expectException(UserException::class);
        $this->expectExceptionMessage('The email has already been taken.');

        // Actions
        $repository->store($userEntity);

        // Assertions
        $this->assertDatabaseCount('users', 1);
    }

    public function testShouldThrowAnUserExceptionWhenTheRegistrationNumberAlreadyExists(): void
    {
        // Set
        UserModel::create([
            'name' => 'some random name',
            'email' => 'some@email.com',
            'registration_number' => '12345678909',
            'type' => 'regular',
            'password' => 'secret',
        ]);

        $repository = app(UserRepository::class);
        $userEntity = UserEntity::newUser(
            name: 'some random name',
            email: 'another@email.com',
            registrationNumber: '12345678909',
            type: 'regular',
            password: 'secret',
        );

        // Expectations
        $this->expectException(UserException::class);
        $this->expectExceptionMessage(
            'The fiscal doc has already been taken.'
        );

        // Actions
        $repository->store($userEntity);

        // Assertions
        $this->assertDatabaseCount('users', 1);
    }
}
