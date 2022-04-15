<?php

namespace Tests\Feature\User;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;
use Tests\TestCase;
use User\Repository;
use User\Store\User;
use User\User as UserModel;
use User\UserException;

class RepositoryTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_should_store_the_new_user(): void
    {
        // Set
        $repository = app(Repository::class);
        $userValueObject = new User([
            'name' => 'some random name',
            'email' => 'some@email.com',
            'registration_number' => '12345678909',
            'type' => 'regular',
            'password' => 'secret',
        ]);

        // Actions
        $result = $repository->store($userValueObject);

        // Assertions
        $this->assertDatabaseCount('users', 1);
        $this->assertInstanceOf(UserModel::class, $result);
        $this->assertSame('Some Random Name', $result->name);
        $this->assertDatabaseHas('users', ['email' => 'some@email.com']);
    }

    public function test_should_throw_an_user_exception_when_the_email_already_exists(): void
    {
        // Set
        UserModel::create([
            'name' => 'some random name',
            'email' => 'some@email.com',
            'registration_number' => '12345678909',
            'type' => 'regular',
            'password' => 'secret',
        ]);

        $repository = app(Repository::class);
        $userValueObject = new User([
            'name' => 'another random name',
            'email' => 'some@email.com',
            'registration_number' => '12345678911',
            'type' => 'regular',
            'password' => 'secret',
        ]);

        // Expectations
        $this->expectException(UserException::class);
        $this->expectExceptionMessage('The email has already been taken.');

        // Actions
        $repository->store($userValueObject);

        // Assertions
        $this->assertDatabaseCount('users', 1);
    }

    public function test_should_throw_an_user_exception_when_the_registration_number_already_exists(): void
    {
        // Set
        UserModel::create([
            'name' => 'some random name',
            'email' => 'some@email.com',
            'registration_number' => '12345678909',
            'type' => 'regular',
            'password' => 'secret',
        ]);

        $repository = app(Repository::class);
        $userValueObject = new User([
            'name' => 'another random name',
            'email' => 'another@email.com',
            'registration_number' => '12345678909',
            'type' => 'regular',
            'password' => 'secret',
        ]);

        // Expectations
        $this->expectException(UserException::class);
        $this->expectExceptionMessage('The fiscal doc has already been taken.');

        // Actions
        $repository->store($userValueObject);

        // Assertions
        $this->assertDatabaseCount('users', 1);
    }

    public function test_should_throw_an_user_exception_when_something_went_wrong_with_the_database(): void
    {
        // Set
        $repository = app(Repository::class);
        $userValueObject = m::mock(User::class);

        // Expectations
        $userValueObject->expects()
            ->getType()
            ->andReturn('regular');

        $userValueObject->expects()
            ->getEmail()
            ->andReturn('some@email.com');

        $userValueObject->expects()
            ->getRegistrationNumber()
            ->andReturn('12345678909');

        $userValueObject->expects()
            ->toArray()
            ->andReturn([
                'name' => null,
                'email' => 'some@email.com',
                'registration_number' => '12345678909',
                'type' => 'regular',
                'password' => 'secret',
            ]);

        $this->expectException(QueryException::class);

        // Actions
        $repository->store($userValueObject);
    }
}
