<?php

namespace Tests\Feature\Transaction\Infra\Http\Controllers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Transaction\Infra\Eloquent\User;
use function bcrypt;
use function route;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

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
