<?php

namespace Transaction\Infra\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;
//use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Transaction\Application\Exceptions\TransferException;
use Transaction\Domain\Entities\Account as AccountEntity;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Eloquent\Account as AccountModel;
use Transaction\Infra\Eloquent\User as UserModel;

class AccountTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    public function test_should_store_a_new_account(): void
    {
        // Set
        $userEntity = m::mock(UserEntity::class);
        $accountModel = $this->instance(AccountModel::class, m::mock(AccountModel::class));
        $repository = new Account();
        UserModel::create([
            'name' => 'Random Name',
            'email' => 'random@email.com',
            'password' => 'secret',
            'registration_number' => '123456890090',
            'type' => 'regular',
        ]);

        // Expectations
        $userEntity->expects()
            ->getId()
            ->andReturn(1);

        $accountModel->shouldReceive('create')
            ->with([
                'number' => m::type('string'),
                'user_id' => 1,
                'amount' => 0,
            ])->andReturn(m::mock(AccountModel::class));

        // Actions
        $result = $repository->store($userEntity);

        // Assertions
        $this->assertInstanceOf(AccountEntity::class, $result);
    }

    public function test_should_proceed_and_update(): void
    {
        // Set
        $user = m::mock(UserEntity::class);
        $account = m::mock(Account::class);
        $collection = m::mock(Collection::class);
        $repository = new Account($account);

        // Expectations
        $user->expects()
            ->getAttribute('id')
            ->andReturn(1);

        $account->expects()
            ->where('id', 1)
            ->andReturn($collection);

        $collection->expects()
            ->first()
            ->andReturn($account);

        $account->expects()
            ->update(['amount' => 10000])
            ->andReturnSelf();

        // Actions
        $repository->update(['amount' => 10000], 1);

        // Assertions
        $this->assertNull(null);
    }

    public function test_should_throw_an_exception_when_the_account_not_found(): void
    {
        // Set
        $user = m::mock(UserEntity::class);
        $account = m::mock(Account::class);
        $collection = m::mock(Collection::class);
        $repository = new Account($account);

        // Expectations
        $user->expects()
            ->getAttribute('id')
            ->andReturn(1);

        $account->expects()
            ->where('id', 1)
            ->andReturn($collection);

        $collection->expects()
            ->first()
            ->andReturnNull();

        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('The informed account was not found on our registers.');

        // Actions
        $repository->update(['amount' => 10000], 1);
    }
}
