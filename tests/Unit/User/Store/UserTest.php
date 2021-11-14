<?php

namespace User\Store;

use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_should_get_users_attributes(): void
    {
        // Set
        $user = new User([
            'name' => 'some random name',
            'email' => 'random@email.com',
            'fiscal_doc' => '123.456.789-09',
            'password' => 'secret',
        ]);

        // Actions
        $name = $user->getName();
        $email = $user->getEmail();
        $fiscalDoc = $user->getFiscalDoc();
        $user->getPassword();

        // Assertions
        $this->assertSame('Some Random Name', $name);
        $this->assertSame('random@email.com', $email);
        $this->assertSame('12345678909', $fiscalDoc);
    }
}
