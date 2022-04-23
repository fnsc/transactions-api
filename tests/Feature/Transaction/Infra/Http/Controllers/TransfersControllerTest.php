<?php

namespace Tests\Feature\Transaction\Infra\Http\Controllers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery as m;
use Tests\TestCase;
use Transaction\Application\Authorization\Service as AuthorizationService;
use Transaction\Domain\Entities\Transaction as TransactionEntity;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Eloquent\User as UserModel;
use Transaction\Infra\Repositories\Account as AccountRepository;

use function app;
use function auth;
use function route;

class TransfersControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function testShouldDoATransferBetweenRegularUsers(): void
    {
        // Set
        $accountRepository = app(AccountRepository::class);
        $authorizationService = $this->instance(
            AuthorizationService::class,
            m::mock(AuthorizationService::class)
        );

        $this->setPayerScenario($accountRepository);
        $this->setPayeeScenario($accountRepository);

        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => 100.27,
        ];

        // Expected
        $authorizationService->expects()
            ->handle(m::type(TransactionEntity::class))
            ->andReturnTrue();

        // Actions
        $result = $this->post(route('api.v1.transfers.store'), $data);

        // Assertions
        $result->assertStatus(Response::HTTP_ACCEPTED);
        $this->assertDatabaseHas('transactions', ['amount' => 10027]);
    }

    public function testShouldRevertTheTransferWhenAuthorizationFails(): void
    {
        // Set
        $accountRepository = app(AccountRepository::class);
        $authorizationService = $this->instance(
            AuthorizationService::class,
            m::mock(AuthorizationService::class)
        );

        $this->setPayerScenario($accountRepository);
        $this->setPayeeScenario($accountRepository);

        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'amount' => 100.27,
        ];

        // Expectations
        $authorizationService->expects()
            ->handle(m::type(TransactionEntity::class))
            ->andReturnFalse();

        // Actions
        $result = $this->post(route('api.v1.transfers.store'), $data);

        // Assertions
        $result->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
        $this->assertDatabaseMissing('transactions', ['amount' => 10027]);
    }

    public function testShouldRedirectWhenSellerUserTryToDoATransfer(): void
    {
        // Set
        $accountRepository = app(AccountRepository::class);

        $payer = UserModel::create([
            'name' => 'Seller User #1',
            'email' => 'seller_number_one@email.com',
            'type' => 'seller',
            'password' => 'secret',
            'registration_number' => '12345678901',
        ]);

        auth()->login($payer);
        $payer = UserEntity::newUser($payer->id, $payer->email, $payer->registration_number, $payer->type);
        $account = $accountRepository->store($payer);
        $account->setAmount(200000);
        $accountRepository->update($account);

        $this->setPayeeScenario($accountRepository);

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
