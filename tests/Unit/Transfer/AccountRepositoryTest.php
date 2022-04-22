<?php

namespace Transfer;

use Illuminate\Database\Eloquent\Collection;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use User\User;

class AccountRepositoryTest extends TestCase
{
    public function test_should_store_a_new_account(): void
    {
        // Set
        $user = m::mock(User::class);
        $account = m::mock(Account::class);
        $repository = new AccountRepository($account);

        // Expectations
        $user->expects()
            ->getAttribute('id')
            ->andReturn(1);

        $account->expects()
            ->create(m::type('array'))
            ->andReturnSelf();

        // Actions
        $result = $repository->store($user);

        // Assertions
        $this->assertInstanceOf(Account::class, $result);
    }

    public function test_should_proceed_and_update(): void
    {
        // Set
        $user = m::mock(User::class);
        $account = m::mock(Account::class);
        $collection = m::mock(Collection::class);
        $repository = new AccountRepository($account);

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
        $user = m::mock(User::class);
        $account = m::mock(Account::class);
        $collection = m::mock(Collection::class);
        $repository = new AccountRepository($account);

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
