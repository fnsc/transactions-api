<?php

namespace Transaction\Application\StoreTransaction;

use Mockery as m;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Transaction\Application\Contracts\AuthenticatedUserAdapter;
use Transaction\Application\Contracts\EventDispatcher;
use Transaction\Application\Events\TransferProcessed;
use Transaction\Application\Exceptions\FraudException;
use Transaction\Application\Exceptions\TransferException;
use Transaction\Domain\Contracts\TransactionRepository;
use Transaction\Domain\Entities\Account as AccountEntity;
use Transaction\Domain\Entities\Transaction;
use Transaction\Domain\Entities\User;
use Transaction\Domain\Contracts\UserRepository;

class ServiceTest extends TestCase
{
    public function testShouldThrowFraudExceptionWhenPayerIdIsDifferent(): void
    {
        // Set
        $transactionRepository = m::mock(TransactionRepository::class);
        $userRepository = m::mock(UserRepository::class);
        $authenticatedUser = m::mock(AuthenticatedUserAdapter::class);
        $evenDispatcher = m::mock(EventDispatcher::class);
        $service = new Service($transactionRepository, $userRepository, $authenticatedUser, $evenDispatcher);
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

    public function testShouldThrowFraudExceptionWhenPayerWasNotFound(): void
    {
        // Set
        $transactionRepository = m::mock(TransactionRepository::class);
        $userRepository = m::mock(UserRepository::class);
        $authenticatedUser = m::mock(AuthenticatedUserAdapter::class);
        $evenDispatcher = m::mock(EventDispatcher::class);
        $service = new Service($transactionRepository, $userRepository, $authenticatedUser, $evenDispatcher);
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

    public function testShouldThrowFraudExceptionWhenPayersAccountDoesNotHaveSufficientAmount(): void
    {
        // Set
        $transactionRepository = m::mock(TransactionRepository::class);
        $userRepository = m::mock(UserRepository::class);
        $authenticatedUser = m::mock(AuthenticatedUserAdapter::class);
        $evenDispatcher = m::mock(EventDispatcher::class);
        $service = new Service($transactionRepository, $userRepository, $authenticatedUser, $evenDispatcher);
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

    public function testShouldThrowFraudExceptionWhenPayeeDoesNotExists(): void
    {
        // Set
        $transactionRepository = m::mock(TransactionRepository::class);
        $userRepository = m::mock(UserRepository::class);
        $authenticatedUser = m::mock(AuthenticatedUserAdapter::class);
        $evenDispatcher = m::mock(EventDispatcher::class);
        $service = new Service($transactionRepository, $userRepository, $authenticatedUser, $evenDispatcher);
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

    public function testShouldHandle(): void
    {
        // Set
        $transactionRepository = $this->createMock(TransactionRepository::class);
        $userRepository = m::mock(UserRepository::class);
        $authenticatedUser = m::mock(AuthenticatedUserAdapter::class);
        $evenDispatcher = $this->createMock(EventDispatcher::class);

        $service = new Service($transactionRepository, $userRepository, $authenticatedUser, $evenDispatcher);

        $payer = User::newUser(id: 2);
        $payer->setAccount(new AccountEntity(amount: 100000, userId: 2, number: 'io12j3oijasodi', id: 1));
        $payee = User::newUser(id: 1);
        $transaction = new Transaction($payee, $payer, 10000);
        $input = new InputBoundary(1, 2, '100.00');

        // Expectations
        $authenticatedUser->expects()
            ->getAuthenticatedUser()
            ->andReturn($payer);

        $userRepository->expects()
            ->find(2)
            ->andReturn($payer);

        $userRepository->expects()
            ->find(1)
            ->andReturn($payee);

        $transactionRepository->expects($this->once())
            ->method('transfer')
            ->with($transaction)
            ->willReturn($transaction);

        $evenDispatcher->expects($this->once())
            ->method('dispatch')
            ->with(new TransferProcessed($transaction));

        // Actions
        $result = $service->handle($input);

        // Assertions
        $this->assertInstanceOf(OutputBoundary::class, $result);
        $this->assertInstanceOf(Transaction::class, $result->getTransaction());
    }
}
