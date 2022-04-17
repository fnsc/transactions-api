<?php

namespace Transaction\Store;

use DateTime;
use Illuminate\Http\Response;
use Mockery as m;
use Money\Money;
use Tests\TestCase;
use Transaction\Application\Exceptions\TransferException;
use Transaction\Application\StoreTransaction\FraudException;
use Transaction\Application\StoreTransaction\Service;
use Transaction\Domain\Entities\User;
use Transaction\Infra\Eloquent\Account;
use Transaction\Infra\Repositories\Transaction;
use Transaction\Infra\Repositories\User as UserRepository;

class ServiceTest extends TestCase
{
    public function test_should_throw_fraud_exception_when_payer_id_is_different(): void
    {
        // Set
        $transactionRepository = m::mock(Transaction::class);
        $userRepository = m::mock(UserRepository::class);
        $service = new Service($transactionRepository, $userRepository);
        $transfer = m::mock(Transaction::class);
        $authUser = m::mock(User::class);

        // Expectations
        $transfer->expects()
            ->getPayerId()
            ->andReturn(1);

        $authUser->expects()
            ->getId()
            ->andReturn(2);

        $this->expectException(FraudException::class);
        $this->expectExceptionMessage('The payer id is different from the user that is currently authenticated.');
        $this->expectExceptionCode(Response::HTTP_NOT_ACCEPTABLE);

        // Actions
        $service->handle($transfer, $authUser);
    }

    public function test_should_throw_fraud_exception_when_payer_was_not_found(): void
    {
        // Set
        $transactionRepository = m::mock(Transaction::class);
        $userRepository = m::mock(UserRepository::class);
        $service = new Service($transactionRepository, $userRepository);
        $transfer = m::mock(Transaction::class);
        $authUser = m::mock(User::class);

        // Expectations
        $transfer->expects()
            ->getPayerId()
            ->twice()
            ->andReturn(1);

        $authUser->expects()
            ->getId()
            ->andReturn(1);

        $userRepository->expects()
            ->find(1)
            ->andReturnNull();

        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('The informed payer was not found on our registers.');
        $this->expectExceptionCode(Response::HTTP_NOT_ACCEPTABLE);

        // Actions
        $service->handle($transfer, $authUser);
    }

    public function test_should_throw_fraud_exception_when_payers_account_does_not_have_sufficient_amount(): void
    {
        // Set
        $transactionRepository = m::mock(Transaction::class);
        $userRepository = m::mock(UserRepository::class);
        $service = new Service($transactionRepository, $userRepository);
        $transfer = m::mock(Transaction::class);
        $authUser = m::mock(User::class);
        $user = m::mock(User::class);
        $account = m::mock(Account::class);
        $money = Money::BRL(100);

        // Expectations
        $transfer->expects()
            ->getPayerId()
            ->twice()
            ->andReturn(1);

        $authUser->expects()
            ->getId()
            ->andReturn(1);

        $userRepository->expects()
            ->find(1)
            ->andReturn($user);

        $user->expects()
            ->getAttribute('account')
            ->andReturn($account);

        $account->expects()
            ->getAmount()
            ->andReturn($money->subtract(Money::BRL(9)));

        $transfer->expects()
            ->getAmount()
            ->andReturn($money);

        $this->expectException(TransferException::class);
        $this->expectExceptionMessage(
            'The payer does not have the sufficient amount on your account to proceed with the operation'
        );
        $this->expectExceptionCode(Response::HTTP_FORBIDDEN);

        // Actions
        $service->handle($transfer, $authUser);
    }

    public function test_should_throw_fraud_exception_when_payee_does_not_exists(): void
    {
        // Set
        $transactionRepository = m::mock(Transaction::class);
        $userRepository = m::mock(UserRepository::class);
        $service = new Service($transactionRepository, $userRepository);
        $transfer = m::mock(Transaction::class);
        $authUser = m::mock(User::class);
        $user = m::mock(User::class);
        $account = m::mock(Account::class);
        $money = Money::BRL(100);

        // Expectations
        $transfer->expects()
            ->getPayerId()
            ->twice()
            ->andReturn(1);

        $authUser->expects()
            ->getId()
            ->andReturn(1);

        $userRepository->expects()
            ->find(1)
            ->andReturn($user);

        $user->expects()
            ->getAttribute('account')
            ->andReturn($account);

        $account->expects()
            ->getAmount()
            ->andReturn($money);

        $transfer->expects()
            ->getAmount()
            ->andReturn($money);

        $transfer->expects()
            ->getPayeeId()
            ->andReturn(2);

        $userRepository->expects()
            ->find(2)
            ->andReturnNull();

        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('The informed payee was not found on our registers.');
        $this->expectExceptionCode(Response::HTTP_NOT_ACCEPTABLE);

        // Actions
        $service->handle($transfer, $authUser);
    }

    public function test_should_handle(): void
    {
        // Set
        $transactionRepository = m::mock(Transaction::class);
        $userRepository = m::mock(UserRepository::class);
        $service = new Service($transactionRepository, $userRepository);
        $transfer = m::mock(Transaction::class);
        $authUser = m::mock(User::class);
        $user = m::mock(User::class);
        $account = m::mock(Account::class);
        $money = Money::BRL(100);
        $transaction = m::mock(Transaction::class);

        // Expectations
        $transfer->expects()
            ->getPayerId()
            ->twice()
            ->andReturn(1);

        $authUser->expects()
            ->getId()
            ->andReturn(1);

        $userRepository->expects()
            ->find(1)
            ->andReturn($user);

        $user->expects()
            ->getAttribute('account')
            ->andReturn($account);

        $account->expects()
            ->getAmount()
            ->andReturn($money);

        $transfer->expects()
            ->getAmount()
            ->andReturn($money);

        $transfer->expects()
            ->getPayeeId()
            ->andReturn(2);

        $userRepository->expects()
            ->find(2)
            ->andReturn($user);

        $transactionRepository->expects()
            ->transfer($transfer, $user, $user)
            ->andReturn($transaction);

        $transaction->expects()
            ->getAttribute('number')
            ->andReturn(m::type('string'));

        $transaction->expects()
            ->getAttribute('payee')
            ->andReturn($user);

        $user->expects()
            ->getAttribute('name')
            ->andReturn('Payee Name');

        $transaction->expects()
            ->getAttribute('payer')
            ->andReturn($user);

        $user->expects()
            ->getAttribute('name')
            ->andReturn('Payer Name');

        $transaction->expects()
            ->getAmount()
            ->andReturn($money);

        $transaction->expects()
            ->getAttribute('created_at')
            ->andReturn(m::mock(DateTime::class));

        // Actions
        $result = $service->handle($transfer, $authUser);

        // Assertions
        $this->assertSame([
            'message' => 'You did it!!!',
            'data' => [],
        ], $result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutEvents();
    }
}
