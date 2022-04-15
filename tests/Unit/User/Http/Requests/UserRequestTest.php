<?php

namespace User\Http\Requests;

use PHPUnit\Framework\TestCase;
use User\Store\User;

class UserRequestTest extends TestCase
{
    public function test_should_return_if_the_class_is_authorized_to_validate_the_data(): void
    {
         // Set
        $userRequest = new LoginRequest();

        // Actions
        $result = $userRequest->authorize();

        // Assertions
        $this->assertTrue($result);
    }

    public function test_should_return_an_array_with_rules(): void
    {
        // Set
        $userRequest = new UserRequest();
        $expected = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,id',
            'registration_number' => 'required|string|fiscal_doc|unique:users,registration_number,id',
            'type' => 'required|string|in:regular,seller',
            'password' => 'required|min:6',
        ];

        // Actions
        $result = $userRequest->rules();

        // Assertions
        $this->assertSame($expected, $result);
    }

    public function test_should_return_an_user_value_object(): void
    {
        // Set
        $userRequest = app(UserRequest::class, [
            'query' => [
                'name' => 'some random name',
                'email' => 'some@random.com',
                'registration_number' => '123.456.789-09',
                'type' => 'seller',
                'password' => 'password',
            ],
        ]);

        // Actions
        $result = $userRequest->getUserValueObject();

        // Assertions
        $this->assertInstanceOf(User::class, $result);
        $this->assertSame('Some Random Name', $result->getName());
    }
}
