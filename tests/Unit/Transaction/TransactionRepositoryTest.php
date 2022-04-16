<?php

namespace Transaction;

use Illuminate\Http\Response;
use Mockery as m;
use Money\Money;
use Tests\TestCase;
use Transaction\Application\Authorization\Service;
use Transaction\Application\StoreTransaction\FraudException;
use Transaction\Domain\Entities\Transaction;
use Transaction\Infra\Eloquent\Account;
use Transaction\Infra\Eloquent\User;
use Transaction\Infra\Repositories\Account;
use Transaction\Infra\Repositories\Transaction;

class TransactionRepositoryTest extends TestCase
{
    public function test_should_store_a_new_transaction(): void
    {
        // Set
        $transaction = m::mock(Transaction::class);
        $accountRepository = m::mock(Account::class);
        $authService = m::mock(Service::class);
        $transfer = m::mock(Transaction::class);
        $repository = new Transaction($transaction, $accountRepository, $authService);
        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => 100,
        ];

        // Expectations
        $transfer->expects()
            ->toArray()
            ->andReturn($data);

        $transaction->expects()
            ->create(m::type('array'))
            ->andReturnSelf();

        // Actions
        $result = $repository->store($transfer);

        // Assertions
        $this->assertInstanceOf(Transaction::class, $result);
    }

    public function test_should_do_a_transfer(): void
    {
        // Set
        $transaction = m::mock(Transaction::class);
        $accountRepository = m::mock(Account::class);
        $authService = m::mock(Service::class);
        $transfer = m::mock(Transaction::class);
        $user = m::mock(User::class);
        $account = m::mock(Account::class);
        $repository = new Transaction($transaction, $accountRepository, $authService);
        $money = Money::BRL(100);
        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => 100,
        ];

        // Expectations
        $user->expects()
            ->getAttribute('account')
            ->twice()
            ->andReturn($account);

        $account->expects()
            ->getAmount()
            ->twice()
            ->andReturn($money);

        $account->expects()
            ->getAttribute('id')
            ->andReturn(1);

        $account->expects()
            ->getAttribute('id')
            ->andReturn(2);

        $transfer->expects()
            ->getAmount()
            ->twice()
            ->andReturn($money);

        $transfer->expects()
            ->toArray()
            ->andReturn($data);

        $transaction->expects()
            ->create(m::type('array'))
            ->andReturnSelf();

        $accountRepository->expects()
            ->update(m::type('array'), 1);

        $accountRepository->expects()
            ->update(m::type('array'), 2);

        $authService->expects()
            ->handle($transaction)
            ->andReturnTrue();

        // Actions
        $result = $repository->transfer($transfer, $user, $user);

        // Assertions
        $this->assertInstanceOf(Transaction::class, $result);
    }

    public function test_should_throw_an_exception_when_the_auth_service_fails(): void
    {
        // Set
        $transaction = m::mock(Transaction::class);
        $accountRepository = m::mock(Account::class);
        $authService = m::mock(Service::class);
        $transfer = m::mock(Transaction::class);
        $user = m::mock(User::class);
        $account = m::mock(Account::class);
        $repository = new Transaction($transaction, $accountRepository, $authService);
        $money = Money::BRL(100);
        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => 100,
        ];

        // Expectations
        $user->expects()
            ->getAttribute('account')
            ->twice()
            ->andReturn($account);

        $account->expects()
            ->getAmount()
            ->twice()
            ->andReturn($money);

        $account->expects()
            ->getAttribute('id')
            ->andReturn(1);

        $account->expects()
            ->getAttribute('id')
            ->andReturn(2);

        $transfer->expects()
            ->getAmount()
            ->twice()
            ->andReturn($money);

        $transfer->expects()
            ->toArray()
            ->andReturn($data);

        $transaction->expects()
            ->create(m::type('array'))
            ->andReturnSelf();

        $accountRepository->expects()
            ->update(m::type('array'), 1);

        $accountRepository->expects()
            ->update(m::type('array'), 2);

        $authService->expects()
            ->handle($transaction)
            ->andReturnFalse();

        $this->expectException(FraudException::class);
        $this->expectExceptionMessage('The authorization service declined the operation.');
        $this->expectExceptionCode(Response::HTTP_NOT_ACCEPTABLE);

        // Actions
        $repository->transfer($transfer, $user, $user);
    }
}
