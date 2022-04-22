<?php

namespace Transaction\Infra\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;
use Money\Money;
use Tests\TestCase;
use Transaction\Application\Authorization\Service;
use Transaction\Application\Exceptions\FraudException;
use Transaction\Domain\Entities\Account as AccountEntity;
use Transaction\Domain\Entities\Transaction as TransactionEntity;
use Transaction\Domain\Entities\User as UserEntity;
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

        $payee = UserEntity::newUser(id: 2);
        $payer = UserEntity::newUser(id: 1);
        
        $transaction = new TransactionEntity(payee: $payee, payer: $payer, amount: 10000);

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

        $payee = UserEntity::newUser(id: 2);
        $payeeAccount = m::mock(AccountEntity::class);
        $payee->setAccount($payeeAccount);

        $payer = UserEntity::newUser(id: 1);
        $payerAccount = m::mock(AccountEntity::class);
        $payer->setAccount($payerAccount);

        $transaction = new TransactionEntity(payee: $payee, payer: $payer, amount: 50000);

        // Expectations
        $payerAccount->expects()
            ->getAmount()
            ->andReturn(Money::BRL(100000));

        $payerAccount->expects()
            ->setAmount(50000);

        $accountRepository->expects()
            ->update($payerAccount);

        $payeeAccount->expects()
            ->getAmount()
            ->andReturn(Money::BRL(100000));

        $payeeAccount->expects()
            ->setAmount(150000);

        $accountRepository->expects()
            ->update($payeeAccount);

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
        $this->setPayerInDatabase();
        $this->setPayeeInDataBase();
        $accountRepository = m::mock(Account::class);
        $authService = m::mock(Service::class);
        $repository = new Transaction($accountRepository, $authService);

        $payee = UserEntity::newUser(id: 2);
        $payeeAccount = m::mock(AccountEntity::class);
        $payee->setAccount($payeeAccount);

        $payer = UserEntity::newUser(id: 1);
        $payerAccount = m::mock(AccountEntity::class);
        $payer->setAccount($payerAccount);

        $transaction = new TransactionEntity(payee: $payee, payer: $payer, amount: 50000);

        // Expectations
        $payerAccount->expects()
            ->getAmount()
            ->andReturn(Money::BRL(100000));

        $payerAccount->expects()
            ->setAmount(50000);

        $accountRepository->expects()
            ->update($payerAccount);

        $payeeAccount->expects()
            ->getAmount()
            ->andReturn(Money::BRL(100000));

        $payeeAccount->expects()
            ->setAmount(150000);

        $accountRepository->expects()
            ->update($payeeAccount);

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
