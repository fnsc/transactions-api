<?php

namespace Tests\Feature\Transaction\Infra\Http\Controllers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Transaction\Infra\Eloquent\User;
use function bcrypt;
use function route;

class StoreUserControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_should_store_a_new_user(): void
    {
        // Set
        $data = [
            'name' => 'some random name',
            'email' => 'some@random.com',
            'registration_number' => '123.456.789-09',
            'type' => 'regular',
            'password' => 'password',
        ];

        // Actions
        $result = $this->post(route('api.v1.users.store'), $data);

        // Assertions
        $result->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('users', ['name' => 'some random name']);
        $this->assertDatabaseHas('users', ['registration_number' => '12345678909']);
    }

    public function test_should_not_store_a_user_with_same_registration_number(): void
    {
        // Set
        $dataOne = [
            'name' => 'some random name',
            'email' => 'some@random.com',
            'registration_number' => '123.456.789-09',
            'type' => 'seller',
            'password' => 'password',
        ];

        $dataTwo = [
            'name' => 'some random name',
            'email' => 'some2@random.com',
            'registration_number' => '123.456.789-09',
            'type' => 'regular',
            'password' => 'password',
        ];

        // Actions
        $this->post(route('api.v1.users.store'), $dataOne);
        $result = $this->post(route('api.v1.users.store'), $dataTwo);

        // Assertions
        $result->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_should_proceed_with_the_login(): void
    {
        // Set
        User::create([
            'name' => 'some random name',
            'email' => 'some@random.com',
            'registration_number' => '12345678909',
            'type' => 'regular',
            'password' => bcrypt('secret'),
        ]);

        $data = [
            'email' => 'some@random.com',
            'password' => 'secret',
        ];

        // Actions
        $result = $this->post(route('api.v1.users.login'), $data);

        // Assertions
        $result->assertStatus(Response::HTTP_ACCEPTED);
    }
}
