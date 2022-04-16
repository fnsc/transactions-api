<?php

namespace Transaction;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticatedUserTest extends TestCase
{
    public function test_should_return_an_authenticated_user(): void
    {
        // Set
        $user = new User();
        $user->id = 1;
        $user->name = 'Some Random Name';
        $user->email = 'random@email.com';
        $user->type = 'regular';
        Auth::login($user);

        $authUser = new User();

        // Actions
        $id = $authUser->getId();
        $name = $authUser->getName();
        $email = $authUser->getEmail();
        $type = $authUser->getType();

        // Assertions
        $this->assertSame(1, $id);
        $this->assertSame('Some Random Name', $name);
        $this->assertSame('random@email.com', $email);
        $this->assertSame('regular', $type);
    }
}
