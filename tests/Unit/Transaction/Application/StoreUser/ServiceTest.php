<?php

namespace Tests\Unit\Transaction\Application\StoreUser;

use Mockery as m;
use Tests\TestCase;
use Transaction\Application\StoreUser\Service;
use Transaction\Application\StoreUser\Transformer;
use Transaction\Infra\Adapters\TokenManager;
use Transaction\Infra\Eloquent\User as UserModel;
use Transaction\Infra\Repositories\User;

class ServiceTest extends TestCase
{
    public function test_should_handle_with_the_new_user_data(): void
    {
        // Set
        $repository = m::mock(User::class);
        $transformer = m::mock(Transformer::class);
        $userValueObject = m::mock(User::class);
        $userModel = m::mock(UserModel::class);
        $manager = m::mock(TokenManager::class);
        $service = new Service($repository, $transformer, $manager);
        $expected = [
            'id' => 1,
            'name' => 'Some Random Name',
        ];

        // Expectations
        $transformer->expects()
            ->transform($userModel, m::type('string'))
            ->andReturn([
                'id' => 1,
                'name' => 'Some Random Name',
            ]);

        $repository->expects()
            ->store($userValueObject)
            ->andReturn($userModel);

        $manager->expects()
            ->manage($userModel)
            ->andReturn('your_new_token');

        // Actions
        $result = $service->handle($userValueObject);

        // Assertions
        $this->assertSame($expected, $result);
    }
}
