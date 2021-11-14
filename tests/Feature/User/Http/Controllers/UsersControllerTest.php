<?php

namespace Tests\Feature\User\Http\Controllers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_should_store_a_new_user(): void
    {
        // Set
        $data = [
            'name' => 'some random name',
            'email' => 'some@random.com',
            'fiscal_doc' => '123.456.789-09',
            'password' => 'password',
        ];

        // Actions
        $result = $this->post(route('api.v1.users.store'), $data);

        // Assertions
        $result->assertStatus(Response::HTTP_CREATED);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['name' => 'Some Random Name']);
        $this->assertDatabaseHas('users', ['fiscal_doc' => '12345678909']);
    }

    public function test_should_not_store_a_user_with_same_fiscal_doc(): void
    {
        // Set
        $dataOne = [
            'name' => 'some random name',
            'email' => 'some@random.com',
            'fiscal_doc' => '123.456.789-09',
            'password' => 'password',
        ];

        $dataTwo = [
            'name' => 'some random name',
            'email' => 'some2@random.com',
            'fiscal_doc' => '123.456.789-09',
            'password' => 'password',
        ];

        // Actions
        $this->post(route('api.v1.users.store'), $dataOne);
        $result = $this->post(route('api.v1.users.store'), $dataTwo);

        // Assertions
        $result->assertStatus(Response::HTTP_CONFLICT);
    }
}
