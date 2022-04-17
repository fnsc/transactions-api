<?php

namespace Tests\Feature\Transaction\Infra\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;
use Tests\TestCase;
use Tests\Unit\Transaction\Application\Authorization\Service as AuthorizationService;
use Transaction\Application\Exceptions\FraudException;
use Transaction\Domain\Entities\Transaction as TransactionEntity;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Eloquent\User as UserModel;
use Transaction\Infra\Repositories\Transaction as TransactionRepository;
use Transaction\Infra\Repositories\User as UserRepository;
use function app;

class TransactionTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_should_store_a_new_transaction(): void
    {
        // Set
        $repository = app(TransactionRepository::class);
        $payee = app(UserRepository::class)->find(2);
        $payer = app(UserRepository::class)->find(1);
        $transfer = new TransactionEntity(
            payee: $payee,
            payer: $payer,
            amount: 11000,
        );

        // Actions
        $result = $repository->store($transfer);

        // Assertions
        $this->assertInstanceOf(TransactionEntity::class, $result);
    }

    public function test_should_proceed_with_a_transfer(): void
    {
        // Set
        $service = $this->instance(AuthorizationService::class, m::mock(AuthorizationService::class));
        $repository = app(TransactionRepository::class);
        $payee = app(UserRepository::class)->find(2);
        $payer = app(UserRepository::class)->find(1);
        $transfer = new TransactionEntity(
            payee: $payee,
            payer: $payer,
            amount: 11000,
        );

        // Expectations
        $service->expects($this->once())
            ->handle(m::type(TransactionEntity::class))
            ->andReturnTrue();

        // Actions
        $result = $repository->transfer($transfer);

        // Assertions
        $this->assertInstanceOf(TransactionEntity::class, $result);
        $this->assertSame('11000', $result->getAmount()->getAmount());
    }

    public function test_should_throw_an_exception_when_authorization_fails(): void
    {
        // Set
        $service = $this->instance(AuthorizationService::class, m::mock(AuthorizationService::class));
        $repository = app(TransactionRepository::class);
        $payee = app(UserRepository::class)->find(2);
        $payer = app(UserRepository::class)->find(1);
        $transfer = new TransactionEntity(
            payee: $payee,
            payer: $payer,
            amount: 11000,
        );

        $payer = UserModel::whereId(1)->first();
        $payee = UserModel::whereId(2)->first();

        // Expectations
        $service->expects()
            ->handle(m::type(TransactionEntity::class))
            ->andReturnFalse();

        $this->expectException(FraudException::class);
        $this->expectExceptionMessage('The authorization service declined the operation.');

        // Actions
        $repository->transfer($transfer, $payer, $payee);
    }

    protected function setUp(): void
    {
        parent::setUp();

        app(UserRepository::class)->store(UserEntity::newUser(
            name: 'Regular User #1',
            email: 'regular_number_one@email.com',
            registrationNumber: '12345678901',
            type: 'regular',
            password: 'secret',
        ));

        app(UserRepository::class)->store(UserEntity::newUser(
            name: 'Seller User #1',
            email: 'seller_number_two@email.com',
            registrationNumber: '98765432101',
            type: 'seller',
            password: 'secret',
        ));
    }
}
