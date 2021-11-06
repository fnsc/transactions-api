<?php

namespace User\Store;

use Mockery as m;
use Tests\TestCase;
use User\Repository;
use User\User as UserModel;

class ServiceTest extends TestCase
{
    public function test_should_handle_with_the_new_user_data(): void
    {
        // Set
        $repository = m::mock(Repository::class);
        $transformer = m::mock(Transformer::class);
        $userValueObject = m::mock(User::class);
        $userModel = m::mock(UserModel::class);
        $service = new Service($repository, $transformer);
        $expected = [
            'id' => 1,
            'name' => 'Some Random Name',
        ];

        // Expectations
        $transformer->expects()
            ->transform($userModel)
            ->andReturn([
                'id' => 1,
                'name' => 'Some Random Name',
            ]);

        $repository->expects()
            ->store($userValueObject)
            ->andReturn($userModel);

        $userModel->expects()
            ->getAuthIdentifier()
            ->andReturn('id');

        // Actions
        $result = $service->handle($userValueObject);

        // Assertions
        $this->assertSame($expected, $result);
    }
}
