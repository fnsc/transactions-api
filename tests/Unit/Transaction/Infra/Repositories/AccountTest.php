<?php

namespace Transaction\Infra\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->setUserDatabase();
        $userEntity = $this->getNewUserEntity();
        $repository = new Account();

        // Actions
        $result = $repository->store($userEntity);

        // Assertions
        $this->assertInstanceOf(AccountEntity::class, $result);
    }

    public function test_should_proceed_and_update(): void
    {
        // Set
        $this->setUserDatabase();
        $this->setAccountDatabase();

        $repository = new Account();
        $accountEntity = new AccountEntity(amount: 10000, userId: 1, id: 1);

        // Actions
        $repository->update($accountEntity);

        // Assertions
        $this->assertNull(null);
    }

    public function test_should_throw_an_exception_when_the_account_not_found(): void
    {
        // Set
        $repository = new Account();
        $accountEntity = new AccountEntity(amount: 10000, userId: 1, id: 1);

        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('The informed account was not found on our registers.');

        // Actions
        $repository->update($accountEntity);
    }

    private function setUserDatabase(): void
    {
        UserModel::create([
            'name' => 'Random Name',
            'email' => 'random@email.com',
            'password' => 'secret',
            'registration_number' => '123456890090',
            'type' => 'regular',
        ]);
    }

    private function getNewUserEntity(): UserEntity
    {
        return UserEntity::newUser(
            id: 1,
            name: 'Random Name',
            email: 'random@email.com',
            registrationNumber: '123456890090',
            type: 'regular',
            password: 'secret',
        );
    }

    private function setAccountDatabase(): void
    {
        AccountModel::create([
            'number' => '6261be3b1ba0f944391c5eb1',
            'user_id' => 1,
            'amount' => 0,
        ]);
    }
}
