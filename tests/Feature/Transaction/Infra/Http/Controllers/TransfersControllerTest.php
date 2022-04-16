<?php

namespace Tests\Feature\Transaction\Infra\Http\Controllers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Transaction\Domain\Entities\Transaction as TransactionEntity;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Eloquent\User as UserModel;
use Transaction\Infra\Repositories\Account as AccountRepository;
use function app;
use function auth;
use function route;
use Mockery as m;
use Transaction\Application\Authorization\Service as AuthorizationService;

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
            $this->createMock(AuthorizationService::class)
        );

        $this->setPayerScenario($accountRepository);
        $this->setPayeeScenario($accountRepository);

        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => 100.27,
        ];

        // Expected
        $authorizationService->expects($this->once())
            ->method('handle')
            ->with(m::type(TransactionEntity::class))
            ->willReturn(true);

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
            $this->createMock(AuthorizationService::class)
        );

        $this->setPayerScenario($accountRepository);
        $this->setPayeeScenario($accountRepository);

        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => 100.27,
        ];

        // Expectations
        $authorizationService->expects($this->once())
            ->method('handle')
            ->with(m::mock(TransactionEntity::class))
            ->willReturn(false);

        // Actions
        $result = $this->post(route('api.v1.transfers.store'), $data);

        // Assertions
        $result->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
        $this->assertDatabaseMissing('transactions', ['amount' => 10027]);
//        $this->assertSame(0, $payee->account->amount);
//        $this->assertSame(200000, $payer->account->amount);
    }

    public function test_should_redirect_when_seller_user_try_to_do_a_transfer(): void
    {
        // Set
        $accountRepository = app(AccountRepository::class);

        $user = UserModel::create([
            'name' => 'Regular User #1',
            'email' => 'regular_number_one@email.com',
            'type' => 'seller',
            'password' => 'secret',
            'registration_number' => '12345678901',
        ]);

        auth()->login($user);
        $account = $accountRepository->store($user);
        $account->amount = 200000;
        $account->update();

        $user = UserModel::create([
            'name' => 'Regular User #2',
            'email' => 'regular_number_two@email.com',
            'type' => 'regular',
            'password' => 'secret',
            'registration_number' => '98765432101',
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
        $result->assertRedirect(route('api.v1.transfers.forbidden'));
    }

    private function setPayerScenario(mixed $accountRepository): void
    {
        $payer = UserModel::create([
            'name' => 'Regular User #1',
            'email' => 'regular_number_one@email.com',
            'type' => 'regular',
            'password' => 'secret',
            'registration_number' => '12345678901',
        ]);

        auth()->login($payer);
        $payer = UserEntity::newUser($payer->id, $payer->email, $payer->registration_number, $payer->type);
        $account = $accountRepository->store($payer);
        $account->setAmount(200000);
        $accountRepository->update($account);
    }

    private function setPayeeScenario(mixed $accountRepository): void
    {
        $payee = UserModel::create([
            'name' => 'Regular User #2',
            'email' => 'regular_number_two@email.com',
            'type' => 'regular',
            'password' => 'secret',
            'registration_number' => '98765432101',
        ]);
        $payee = UserEntity::newUser($payee->id, $payee->email, $payee->registration_number, $payee->type);
        $accountRepository->store($payee);
    }
}
