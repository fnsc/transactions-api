<?php

namespace Tests\Feature\Transfer;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Transfer\Account;
use Transfer\AccountRepository;
use User\User;

class AccountRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_should_return_the_required_account(): void
    {
        // Set
        Account::create([
            'number' => '61a3c6e78e832a50830b8bb1',
            'user_id' => 1,
            'amount' => 1000,
        ]);

        $repository = app(AccountRepository::class);

        // Actions
        $result = $repository->find(1);

        // Assertions
        $this->assertInstanceOf(Account::class, $result);
        $this->assertSame('61a3c6e78e832a50830b8bb1', $result->getAttribute('number'));
    }

    public function test_should_return_null_when_the_account_not_found(): void
    {
        // Set
        $repository = app(AccountRepository::class);

        // Actions
        $result = $repository->find(1);

        // Assertions
        $this->assertNull($result);
    }

    public function test_should_store_a_new_account(): void
    {
        // Set
        $repository = app(AccountRepository::class);
        $user = User::first();

        // Actions
        $result = $repository->store($user);

        // Assertions
        $this->assertInstanceOf(Account::class, $result);
        $this->assertSame(0, $result->getAttribute('amount'));
    }

    public function test_should_update_the_given_account(): void
    {
        // Set
        $repository = app(AccountRepository::class);
        Account::create([
            'number' => '61a3c6e78e832a50830b8bb1',
            'user_id' => 1,
            'amount' => 1000,
        ]);

        $data = [
            'number' => 'random number',
            'amount' => 200,
        ];

        // Actions
        $repository->update($data, 1);
        $account = Account::first();

        // Assertions
        $this->assertSame('200', $account->getAttribute('amount'));
        $this->assertSame('61a3c6e78e832a50830b8bb1', $account->getAttribute('number'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        User::create([
            'name' => 'Regular User #1',
            'email' => 'regular_number_one@email.com',
            'type' => 'regular',
            'password' => 'secret',
            'fiscal_doc' => '12345678901',
        ]);
    }
}
