<?php

namespace Tests\Feature\Transfer\Http\Controllers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery as m;
use Tests\TestCase;
use Transfer\AccountRepository;
use Transfer\Authorization\Service as AuthorizationService;
use Transfer\Transaction;
use User\User;

class TransfersControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function test_should_do_a_transfer_between_regular_users(): void
    {
        // Set
        $accountRepository = app(AccountRepository::class);
        $authorizationService = $this->instance(
            AuthorizationService::class,
            m::mock(AuthorizationService::class)
        );

        $user = User::create([
            'name' => 'Regular User #1',
            'email' => 'regular_number_one@email.com',
            'type' => 'regular',
            'password' => 'secret',
            'fiscal_doc' => '12345678901',
        ]);

        auth()->login($user);
        $account = $accountRepository->store($user);
        $account->amount = 200000;
        $account->update();

        $user = User::create([
            'name' => 'Regular User #2',
            'email' => 'regular_number_two@email.com',
            'type' => 'regular',
            'password' => 'secret',
            'fiscal_doc' => '98765432101',
        ]);
        $accountRepository->store($user);

        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => 100.27,
        ];

        // Expectations
        $authorizationService->expects()
            ->handle(m::type(Transaction::class))
            ->andReturnTrue();

        // Actions
        $result = $this->post(route('api.v1.transfers.store'), $data);

        // Assertions
        $result->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertDatabaseHas('transactions', ['amount' => 10027]);
    }

    public function test_should_revert_the_transfer_when_authorization_fails(): void
    {
        // Set
        $accountRepository = app(AccountRepository::class);
        $authorizationService = $this->instance(
            AuthorizationService::class,
            m::mock(AuthorizationService::class)
        );

        $payer = User::create([
            'name' => 'Regular User #1',
            'email' => 'regular_number_one@email.com',
            'type' => 'regular',
            'password' => 'secret',
            'fiscal_doc' => '12345678901',
        ]);

        auth()->login($payer);
        $account = $accountRepository->store($payer);
        $account->amount = 200000;
        $account->update();

        $payee = User::create([
            'name' => 'Regular User #2',
            'email' => 'regular_number_two@email.com',
            'type' => 'regular',
            'password' => 'secret',
            'fiscal_doc' => '98765432101',
        ]);
        $accountRepository->store($payee);

        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => 100.27,
        ];

        // Expectations
        $authorizationService->expects()
            ->handle(m::type(Transaction::class))
            ->andReturnFalse();

        // Actions
        $result = $this->post(route('api.v1.transfers.store'), $data);

        // Assertions
        $result->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
        $this->assertDatabaseMissing('transactions', ['amount' => 10027]);
        $this->assertSame('0', $payee->account->amount);
        $this->assertSame('200000', $payer->account->amount);
    }

    public function test_should_redirect_when_seller_user_try_to_do_a_transfer(): void
    {
        // Set
        $accountRepository = app(AccountRepository::class);

        $user = User::create([
            'name' => 'Regular User #1',
            'email' => 'regular_number_one@email.com',
            'type' => 'seller',
            'password' => 'secret',
            'fiscal_doc' => '12345678901',
        ]);

        auth()->login($user);
        $account = $accountRepository->store($user);
        $account->amount = 200000;
        $account->update();

        $user = User::create([
            'name' => 'Regular User #2',
            'email' => 'regular_number_two@email.com',
            'type' => 'regular',
            'password' => 'secret',
            'fiscal_doc' => '98765432101',
        ]);
        $accountRepository->store($user);

        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => 100.27,
        ];

        // Actions
        $result = $this->post(route('api.v1.transfers.store'), $data);

        // Assertions
        $result->assertStatus(Response::HTTP_FORBIDDEN);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutEvents();
    }
}
