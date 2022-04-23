<?php

namespace Tests\Feature\Transaction\Infra\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Transaction\Domain\Entities\Account as AccountEntity;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Eloquent\Account as AccountModel;
use Transaction\Infra\Eloquent\User as UserModel;
use Transaction\Infra\Repositories\Account as AccountRepository;

class AccountTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function testShouldReturnTheRequiredAccount(): void
    {
        // Set
        $this->setAccountDatabase();

        $repository = app(AccountRepository::class);

        // Actions
        $result = $repository->find(new AccountEntity(id: 1));

        // Assertions
        $this->assertInstanceOf(AccountEntity::class, $result);
        $this->assertSame('61a3c6e78e832a50830b8bb1', $result->getNumber());
    }

    public function testShouldReturnNullWhenTheAccountNotFound(): void
    {
        // Set
        $repository = new AccountRepository();

        // Actions
        $result = $repository->find(new AccountEntity(id: 1));

        // Assertions
        $this->assertNull($result);
    }

    public function testShouldFindAnAccountByUser(): void
    {
        // Set
        $this->setAccountDatabase();
        $user = UserEntity::newUser(id: 1);
        $repository = new AccountRepository();

        // Actions
        $result = $repository->findByUser($user);

        // Assertions
        $this->assertInstanceOf(AccountEntity::class, $result);
    }

    public function testShouldStoreANewAccount(): void
    {
        // Set
        $repository = app(AccountRepository::class);

        // Actions
        $result = $repository->store(UserEntity::newUser(id: 1));

        // Assertions
        $this->assertInstanceOf(AccountEntity::class, $result);
        $this->assertSame('0', $result->getAmount()->getAmount());
    }

    public function testShouldUpdateTheGivenAccount(): void
    {
        // Set
        $repository = app(AccountRepository::class);
        AccountModel::create([
            'number' => '61a3c6e78e832a50830b8bb1',
            'user_id' => 1,
            'amount' => 1000,
        ]);

        // Actions
        $repository->update(
            new AccountEntity(
                amount: 200,
                number: 'random number',
                id: 1
            )
        );
        $account = AccountModel::first();

        // Assertions
        $this->assertSame(200, $account->getAttribute('amount'));
        $this->assertSame('61a3c6e78e832a50830b8bb1', $account->getAttribute('number'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        UserModel::create([
            'name' => 'Regular User #1',
            'email' => 'regular_number_one@email.com',
            'type' => 'regular',
            'password' => 'secret',
            'registration_number' => '12345678901',
        ]);
    }

    private function setAccountDatabase(): void
    {
        AccountModel::create([
            'number' => '61a3c6e78e832a50830b8bb1',
            'user_id' => 1,
            'amount' => 1000,
        ]);
    }
}
