<?php

namespace Transaction\Application\StoreTransaction;

use Mockery as m;
use Money\Money;
use Tests\TestCase;
//use PHPUnit\Framework\TestCase;
use Transaction\Application\Contracts\AuthenticatedUserAdapter;
use Transaction\Application\Contracts\EventDispatcher;
use Transaction\Application\Events\TransferProcessed;
use Transaction\Application\Exceptions\FraudException;
use Transaction\Application\Exceptions\TransferException;
use Transaction\Domain\Contracts\TransactionRepository;
use Transaction\Domain\Entities\Account as AccountEntity;
use Transaction\Domain\Entities\Transaction;
use Transaction\Domain\Entities\User;
use Transaction\Infra\Eloquent\Account;
use Transaction\Domain\Contracts\UserRepository;

class ServiceTest extends TestCase
{
    public function test_should_throw_fraud_exception_when_payer_id_is_different(): void
    {
        // Set
        $transactionRepository = m::mock(TransactionRepository::class);
        $userRepository = m::mock(UserRepository::class);
        $authenticatedUser = m::mock(AuthenticatedUserAdapter::class);
        $evenDispatcher = m::mock(EventDispatcher::class);
        $service = new Service(
            $transactionRepository,
            $userRepository,
            $authenticatedUser,
            $evenDispatcher
        );
        $user = m::mock(User::class);
        $input = new InputBoundary(1, 3, '100.00');

        // Expectations
        $authenticatedUser->expects()
            ->getAuthenticatedUser()
            ->andReturn($user);

        $user->expects()
            ->getId()
            ->andReturn(2);

        $this->expectException(FraudException::class);
        $this->expectExceptionMessage('The payer id is different from the user that is currently authenticated.');

        // Actions
        $service->handle($input);
    }

    public function test_should_throw_fraud_exception_when_payer_was_not_found(): void
    {
        // Set
        $transactionRepository = m::mock(TransactionRepository::class);
        $userRepository = m::mock(UserRepository::class);
        $authenticatedUser = m::mock(AuthenticatedUserAdapter::class);
        $evenDispatcher = m::mock(EventDispatcher::class);
        $service = new Service(
            $transactionRepository,
            $userRepository,
            $authenticatedUser,
            $evenDispatcher
        );
        $user = m::mock(User::class);
        $input = new InputBoundary(1, 2, '100.00');

        // Expectations
        $authenticatedUser->expects()
            ->getAuthenticatedUser()
            ->andReturn($user);

        $user->expects()
            ->getId()
            ->andReturn(2);

        $userRepository->expects()
            ->find(2)
            ->andReturnNull();

        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('The informed payer was not found on our registers.');

        // Actions
        $service->handle($input);
    }

    public function test_should_throw_fraud_exception_when_payers_account_does_not_have_sufficient_amount(): void
    {
        // Set
        $transactionRepository = m::mock(TransactionRepository::class);
        $userRepository = m::mock(UserRepository::class);
        $authenticatedUser = m::mock(AuthenticatedUserAdapter::class);
        $evenDispatcher = m::mock(EventDispatcher::class);
        $service = new Service(
            $transactionRepository,
            $userRepository,
            $authenticatedUser,
            $evenDispatcher
        );
        $user = m::mock(User::class);
        $account = m::mock(AccountEntity::class);
        $input = new InputBoundary(1, 2, '100.00');

        // Expectations
        $authenticatedUser->expects()
            ->getAuthenticatedUser()
            ->andReturn($user);

        $user->expects()
            ->getId()
            ->andReturn(2);

        $userRepository->expects()
            ->find(2)
            ->andReturn($user);

        $user->expects()
            ->getAccount()
            ->andReturn($account);

        $account->expects()
            ->getAmount()
            ->andReturn(Money::BRL(0));

        $this->expectException(TransferException::class);
        $this->expectExceptionMessage(
            'The payer does not have the sufficient amount on your account to proceed with the operation'
        );

        // Actions
        $service->handle($input);
    }

    public function test_should_throw_fraud_exception_when_payee_does_not_exists(): void
    {
        // Set
        $transactionRepository = m::mock(TransactionRepository::class);
        $userRepository = m::mock(UserRepository::class);
        $authenticatedUser = m::mock(AuthenticatedUserAdapter::class);
        $evenDispatcher = m::mock(EventDispatcher::class);
        $service = new Service(
            $transactionRepository,
            $userRepository,
            $authenticatedUser,
            $evenDispatcher
        );
        $user = m::mock(User::class);
        $account = m::mock(AccountEntity::class);
        $input = new InputBoundary(1, 2, '100.00');

        // Expectations
        $authenticatedUser->expects()
            ->getAuthenticatedUser()
            ->andReturn($user);

        $user->expects()
            ->getId()
            ->andReturn(2);

        $userRepository->expects()
            ->find(2)
            ->andReturn($user);

        $user->expects()
            ->getAccount()
            ->andReturn($account);

        $account->expects()
            ->getAmount()
            ->andReturn(Money::BRL(20000));

        $userRepository->expects()
            ->find(1)
            ->andReturnNull();

        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('The informed payee was not found on our registers.');

        // Actions
        $service->handle($input);
    }

    public function test_should_handle(): void
    {
        // Set
        $transactionRepository = $this->createMock(TransactionRepository::class);
        $userRepository = m::mock(UserRepository::class);
        $authenticatedUser = m::mock(AuthenticatedUserAdapter::class);
        $evenDispatcher = m::mock(EventDispatcher::class);
        $service = new Service(
            $transactionRepository,
            $userRepository,
            $authenticatedUser,
            $evenDispatcher
        );
        $user = m::mock(User::class);
        $account = m::mock(AccountEntity::class);
        $transaction = m::mock(Transaction::class);
        $transactionDatabse = m::mock(Transaction::class);
        $input = new InputBoundary(1, 2, '100.00');

        // Expectations
        $authenticatedUser->expects()
            ->getAuthenticatedUser()
            ->andReturn($user);

        $user->expects()
            ->getId()
            ->andReturn(2);

        $userRepository->expects()
            ->find(2)
            ->andReturn($user);

        $user->expects()
            ->getAccount()
            ->andReturn($account);

        $account->expects()
            ->getAmount()
            ->andReturn(Money::BRL(20000));

        $userRepository->expects()
            ->find(1)
            ->andReturn($user);

        $transactionRepository->expects($this->once())
            ->method('transfer')
            ->with($transaction)
            ->willReturn($transactionDatabse);

        $evenDispatcher->expects()
            ->dispatch(m::mock(TransferProcessed::class, [$transactionDatabse]));

        // Actions
        $result = $service->handle($input);

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
