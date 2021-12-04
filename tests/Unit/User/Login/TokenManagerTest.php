<?php

namespace User\Login;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Sanctum\NewAccessToken;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use User\User;

class TokenManagerTest extends TestCase
{
    public function test_should_manage_user_auth_tokens(): void
    {
        // Set
        $user = m::mock(User::class);
        $manager = new TokenManager();
        $relation = m::mock(MorphMany::class);
        $token = m::mock(NewAccessToken::class)->makePartial();
        $expected = 'your_new_access_token';
        $token->plainTextToken = $expected;

        // Expectations
        $user->expects()
            ->tokens()
            ->andReturn($relation);

        $relation->expects()
            ->delete();

        $user->expects()
            ->createToken('auth')
            ->andReturn($token);

        // Actions
        $result = $manager->manage($user);

        // Assertions
        $this->assertSame($expected, $result);
    }
}
