<?php

namespace Tests\Feature\Transfer;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;
use Tests\TestCase;
use Transfer\Authorization\Service as AuthorizationService;
use Transfer\FraudException;
use Transfer\Store\Transfer;
use Transfer\Transaction;
use Transfer\TransactionRepository;
use User\Repository as UserRepository;
use User\Store\User as UserValueObject;
use User\User;

class TransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_should_store_a_new_transaction(): void
    {
        // Set
        $repository = app(TransactionRepository::class);
        $transfer = new Transfer([
            'payee_id' => 2,
            'payer_id' => 1,
            'amount' => '110.00',
        ]);

        // Actions
        $result = $repository->store($transfer);

        // Assertions
        $this->assertInstanceOf(Transaction::class, $result);
    }

    public function test_should_proceed_with_a_transfer(): void
    {
        // Set
        $service = $this->instance(AuthorizationService::class, m::mock(AuthorizationService::class));
        $repository = app(TransactionRepository::class);
        $transfer = new Transfer([
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => '110.00',
        ]);

        $payer = User::whereId(1)->first();
        $payee = User::whereId(2)->first();

        // Expectations
        $service->expects()
            ->handle(m::type(Transaction::class))
            ->andReturnTrue();

        // Actions
        $result = $repository->transfer($transfer, $payer, $payee);

        // Assertions
        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertSame('11000', $result->getAmount()->getAmount());
    }

    public function test_should_throw_an_exception_when_authorization_fails(): void
    {
        // Set
        $service = $this->instance(AuthorizationService::class, m::mock(AuthorizationService::class));
        $repository = app(TransactionRepository::class);
        $transfer = new Transfer([
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => '110.00',
        ]);

        $payer = User::whereId(1)->first();
        $payee = User::whereId(2)->first();

        // Expectations
        $service->expects()
            ->handle(m::type(Transaction::class))
            ->andReturnFalse();

        $this->expectException(FraudException::class);
        $this->expectExceptionMessage('The authorization service declined the operation.');

        // Actions
        $repository->transfer($transfer, $payer, $payee);
    }

    protected function setUp(): void
    {
        parent::setUp();

        app(UserRepository::class)->store(new UserValueObject([
            'name' => 'Regular User #1',
            'email' => 'regular_number_one@email.com',
            'type' => 'regular',
            'password' => 'secret',
            'registration_number' => '12345678901',
        ]));

        app(UserRepository::class)->store(new UserValueObject([
            'name' => 'Seller User #1',
            'email' => 'seller_number_two@email.com',
            'type' => 'seller',
            'password' => 'secret',
            'registration_number' => '98765432101',
        ]));
    }
}
