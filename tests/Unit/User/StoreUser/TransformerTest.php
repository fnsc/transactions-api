<?php

namespace User\StoreUser;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Transaction\Application\StoreUser\Transformer;
use Transaction\Infra\Eloquent\Account;
use Transaction\Infra\Eloquent\User;

class TransformerTest extends TestCase
{
    public function test_should_transform_the_user(): void
    {
        // Set
        $transformer = new Transformer();
        $user = m::mock(User::class);
        $account = m::mock(Account::class);
        $token = 'your_new_auth_token';
        $expected = [
            'id' => 1,
            'name' => 'Random Name',
            'type' => 'regular',
            'account' => [
                'number' => 'some random account number',
            ],
            'token' => 'your_new_auth_token',
        ];

        // Expectations
        $user->expects()
            ->getAttribute('id')
            ->andReturn(1);

        $user->expects()
            ->getAttribute('name')
            ->andReturn('Random Name');

        $user->expects()
            ->getAttribute('type')
            ->andReturn('regular');

        $user->expects()
            ->getAttribute('account')
            ->andReturn($account);

        $account->expects()
            ->getAttribute('number')
            ->andReturn('some random account number');

        // Actions
        $result = $transformer->transform($user, $token);

        // Assertions
        $this->assertSame($expected, $result);
    }
}
