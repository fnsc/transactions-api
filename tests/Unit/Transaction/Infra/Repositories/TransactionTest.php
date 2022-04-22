<?php

namespace Transaction\Infra\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery as m;
use Money\Money;
use Tests\TestCase;
use Transaction\Application\Authorization\Service;
use Transaction\Application\Exceptions\FraudException;
use Transaction\Domain\Entities\Account as AccountEntity;
use Transaction\Domain\Entities\Transaction as TransactionEntity;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Eloquent\Account as AccountModel;
use Transaction\Infra\Eloquent\User as UserModel;

class TransactionTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    public function test_should_store_a_new_transaction(): void
    {
        // Set
        $this->setPayerInDatabase();
        $this->setPayeeInDataBase();
        $accountRepository = m::mock(Account::class);
        $authService = m::mock(Service::class);
        $repository = new Transaction($accountRepository, $authService);

        $transaction = m::mock(TransactionEntity::class);
        $payee = m::mock(UserEntity::class);
        $payer = m::mock(UserEntity::class);

        // Expectations
        $transaction->expects()
            ->getPayee()
            ->andReturn($payee);

        $payee->expects()
            ->getId()
            ->andReturn(2);

        $transaction->expects()
            ->getPayer()
            ->andReturn($payer);

        $payer->expects()
            ->getId()
            ->andReturn(1);

        $transaction->expects()
            ->getAmount()
            ->andReturn(Money::BRL(10000));

        // Actions
        $result = $repository->store($transaction);

        // Assertions
        $this->assertInstanceOf(TransactionEntity::class, $result);
        $this->assertSame(2, $result->getPayee()->getId());
        $this->assertSame(1, $result->getPayer()->getId());
    }

    public function test_should_do_a_transfer(): void
    {
        // Set
        $this->setPayerInDatabase();
        $this->setPayeeInDataBase();
        $accountRepository = m::mock(Account::class);
        $authService = m::mock(Service::class);
        $repository = new Transaction($accountRepository, $authService);

        $transaction = m::mock(TransactionEntity::class);
        $payee = m::mock(UserEntity::class);
        $payeeAccount = m::mock(AccountEntity::class);
        $payer = m::mock(UserEntity::class);
        $payerAccount = m::mock(AccountEntity::class);

        // Expectations
        $transaction->expects()
            ->getPayer()
            ->twice()
            ->andReturn($payer);

        $payer->expects()
            ->getAccount()
            ->andReturn($payerAccount);

        $payerAccount->expects()
            ->getAmount()
            ->andReturn(Money::BRL(100000));

        $transaction->expects()
            ->getAmount()
            ->times(3)
            ->andReturn(Money::BRL(50000));

        $payerAccount->expects()
            ->setAmount(50000);

        $accountRepository->expects()
            ->update($payerAccount);

        $transaction->expects()
            ->getPayee()
            ->twice()
            ->andReturn($payee);

        $payee->expects()
            ->getAccount()
            ->andReturn($payeeAccount);

        $payeeAccount->expects()
            ->getAmount()
            ->andReturn(Money::BRL(100000));

        $payeeAccount->expects()
            ->setAmount(150000);

        $accountRepository->expects()
            ->update($payeeAccount);

        $payee->expects()
            ->getId()
            ->andReturn(2);

        $payer->expects()
            ->getId()
            ->andReturn(1);

        $authService->expects()
            ->handle(m::type(TransactionEntity::class))
            ->andReturnTrue();

        // Actions
        $result = $repository->transfer($transaction);

        // Assertions
        $this->assertInstanceOf(TransactionEntity::class, $result);
    }

    public function test_should_throw_an_exception_when_the_auth_service_fails(): void
    {
        // Set
        // Set
        $this->setPayerInDatabase();
        $this->setPayeeInDataBase();
        $accountRepository = m::mock(Account::class);
        $authService = m::mock(Service::class);
        $repository = new Transaction($accountRepository, $authService);

        $transaction = m::mock(TransactionEntity::class);
        $payee = m::mock(UserEntity::class);
        $payeeAccount = m::mock(AccountEntity::class);
        $payer = m::mock(UserEntity::class);
        $payerAccount = m::mock(AccountEntity::class);

        // Expectations
        $transaction->expects()
            ->getPayer()
            ->twice()
            ->andReturn($payer);

        $payer->expects()
            ->getAccount()
            ->andReturn($payerAccount);

        $payerAccount->expects()
            ->getAmount()
            ->andReturn(Money::BRL(100000));

        $transaction->expects()
            ->getAmount()
            ->times(3)
            ->andReturn(Money::BRL(50000));

        $payerAccount->expects()
            ->setAmount(50000);

        $accountRepository->expects()
            ->update($payerAccount);

        $transaction->expects()
            ->getPayee()
            ->twice()
            ->andReturn($payee);

        $payee->expects()
            ->getAccount()
            ->andReturn($payeeAccount);

        $payeeAccount->expects()
            ->getAmount()
            ->andReturn(Money::BRL(100000));

        $payeeAccount->expects()
            ->setAmount(150000);

        $accountRepository->expects()
            ->update($payeeAccount);

        $payee->expects()
            ->getId()
            ->andReturn(2);

        $payer->expects()
            ->getId()
            ->andReturn(1);

        $authService->expects()
            ->handle(m::type(TransactionEntity::class))
            ->andReturnFalse();

        $this->expectException(FraudException::class);
        $this->expectExceptionMessage('The authorization service declined the operation.');

        // Actions
        $repository->transfer($transaction);
    }

    private function setPayerInDatabase(): void
    {
        UserModel::create([
            'name' => 'Payer Name',
            'email' => 'payer@email.com',
            'password' => 'secret',
            'registration_number' => '123456890090',
            'type' => 'regular',
        ]);
    }

    private function setPayeeInDataBase(): void
    {
        UserModel::create([
            'name' => 'Payee Name',
            'email' => 'payee@email.com',
            'password' => 'secret',
            'registration_number' => '98765432101',
            'type' => 'regular',
        ]);
    }
}
