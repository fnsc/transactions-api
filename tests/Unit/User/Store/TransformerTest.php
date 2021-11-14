<?php

namespace User\Store;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use User\User;

class TransformerTest extends TestCase
{
    public function test_should_transform_the_user(): void
    {
        // Set
        $transformer = new Transformer();
        $user = m::mock(User::class);
        $expected = [
            'id' => 1,
            'name' => 'Random Name',
        ];

        // Expectations
        $user->expects()
            ->getAttribute('id')
            ->andReturn(1);

        $user->expects()
            ->getAttribute('name')
            ->andReturn('Random Name');

        // Actions
        $result = $transformer->transform($user);

        // Assertions
        $this->assertSame($expected, $result);
    }
}
