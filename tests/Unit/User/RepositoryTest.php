<?php

namespace User;

use Illuminate\Support\Collection;
use Mockery as m;
use Tests\TestCase;
use Transfer\Account;
use Transfer\AccountRepository;
use User\Store\User as UserValueObject;

class RepositoryTest extends TestCase
{
    public function test_should_store_the_new_resource(): void
    {
        // Set
        $userModel = m::mock(User::class);
        $userValueObject = m::mock(UserValueObject::class);
        $collection = m::mock(Collection::class);
        $accountRepository = m::mock(AccountRepository::class);
        $repository = new Repository($userModel, $accountRepository);
        $userData = [
            'name' => 'Some Random Name',
            'email' => 'random@email.com',
            'fiscal_doc' => '12345678909',
            'password' => 'secret',
            'type' => 'seller',
        ];

        // Expectations
        $userValueObject->expects()
            ->getType()
            ->andReturn('seller');

        $userValueObject->expects()
            ->getEmail()
            ->andReturn('random@email.com');

        $userValueObject->expects()
            ->getFiscalDoc()
            ->andReturn('12345678909');

        $userValueObject->expects()
            ->toArray()
            ->andReturn($userData);

        $userModel->expects()
            ->where('email', 'random@email.com')
            ->andReturn($collection);

        $userModel->expects()
            ->where('fiscal_doc', '12345678909')
            ->andReturn($collection);

        $userModel->expects()
            ->create($userData)
            ->andReturnSelf();

        $collection->expects()
            ->first()
            ->twice()
            ->andReturnNull();

        $accountRepository->expects()
            ->store($userModel)
            ->andReturn(m::mock(Account::class));

        // Actions
        $result = $repository->store($userValueObject);

        // Assertions
        $this->assertInstanceOf(User::class, $result);
    }

    public function test_should_throw_and_exception_when_email_already_exists(): void
    {
        // Set
        $userModel = m::mock(User::class);
        $userValueObject = m::mock(UserValueObject::class);
        $collection = m::mock(Collection::class);
        $accountRepository = m::mock(AccountRepository::class);
        $repository = new Repository($userModel, $accountRepository);

        // Expectations
        $userValueObject->expects()
            ->getType()
            ->andReturn('seller');

        $userValueObject->expects()
            ->getEmail()
            ->andReturn('random@email.com');

        $userModel->expects()
            ->where('email', 'random@email.com')
            ->andReturn($collection);

        $collection->expects()
            ->first()
            ->andReturn($userModel);

        $this->expectException(UserException::class);
        $this->expectExceptionMessage('The email has already been taken.');

        // Actions
        $repository->store($userValueObject);
    }

    public function test_should_throw_an_user_exception_when_fiscal_doc_already_exists(): void
    {
        // Set
        $userModel = m::mock(User::class);
        $userValueObject = m::mock(UserValueObject::class);
        $collection = m::mock(Collection::class);
        $accountRepository = m::mock(AccountRepository::class);
        $repository = new Repository($userModel, $accountRepository);

        // Expectations
        $userValueObject->expects()
            ->getType()
            ->andReturn('seller');

        $userValueObject->expects()
            ->getEmail()
            ->andReturn('random@email.com');

        $userValueObject->expects()
            ->getFiscalDoc()
            ->andReturn('12345678909');

        $userModel->expects()
            ->where('email', 'random@email.com')
            ->andReturn($collection);

        $userModel->expects()
            ->where('fiscal_doc', '12345678909')
            ->andReturn($collection);

        $collection->expects()
            ->first()
            ->andReturnNull();

        $collection->expects()
            ->first()
            ->andReturn($userModel);

        $this->expectException(UserException::class);
        $this->expectExceptionMessage('The fiscal doc has already been taken.');

        // Actions
        $result = $repository->store($userValueObject);

        // Assertions
        $this->assertInstanceOf(User::class, $result);
    }

    public function test_should_throw_an_user_exception_when_something_goes_wrong_with_the_database(): void
    {
        // Set
        $userModel = m::mock(User::class);
        $userValueObject = m::mock(UserValueObject::class);
        $collection = m::mock(Collection::class);
        $accountRepository = m::mock(AccountRepository::class);
        $repository = new Repository($userModel, $accountRepository);
        $userData = [
            'name' => 'Some Random Name',
            'email' => 'random@email.com',
            'fiscal_doc' => '12345678909',
            'password' => 'secret',
            'type' => 'regular',
        ];

        // Expectations
        $userValueObject->expects()
            ->getType()
            ->andReturn('regular');

        $userValueObject->expects()
            ->getEmail()
            ->andReturn('random@email.com');

        $userValueObject->expects()
            ->getFiscalDoc()
            ->andReturn('12345678909');

        $userValueObject->expects()
            ->toArray()
            ->andReturn($userData);

        $userModel->expects()
            ->where('email', 'random@email.com')
            ->andReturn($collection);

        $userModel->expects()
            ->where('fiscal_doc', '12345678909')
            ->andReturn($collection);

        $userModel->expects()
            ->create($userData)
            ->andReturnNull();

        $collection->expects()
            ->first()
            ->twice()
            ->andReturnNull();

        $this->expectException(UserException::class);
        $this->expectExceptionMessage('The new user cannot be stored.');

        // Actions
        $repository->store($userValueObject);
    }

    public function test_should_throw_an_exception_when_user_type_is_invalid(): void
    {
        // Set
        $userModel = m::mock(User::class);
        $userValueObject = m::mock(UserValueObject::class);
        $accountRepository = m::mock(AccountRepository::class);
        $repository = new Repository($userModel, $accountRepository);

        // Expectations
        $userValueObject->expects()
            ->getType()
            ->andReturn('random');

        $this->expectException(UserException::class);
        $this->expectExceptionMessage('The user type is invalid.');

        // Actions
        $repository->store($userValueObject);
    }
}
