<?php

namespace Tests\Unit\Transaction\Infra\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Mockery as m;
use Psr\Log\LoggerInterface;
use Tests\TestCase;
use Transaction\Application\Exceptions\LoginException;
use Transaction\Application\Exceptions\UserException;
use Transaction\Application\Login\Service as LoginService;
use Transaction\Application\StoreUser\Service;
use Transaction\Application\StoreUser\User;
use Transaction\Infra\Http\Controllers\StoreUsersController;
use Transaction\Infra\Http\Requests\LoginRequest;
use Transaction\Infra\Http\Requests\UserRequest;

class UsersControllerTest extends TestCase
{
    public function test_should_save_the_new_user(): void
    {
        // Set
        $request = m::mock(UserRequest::class);
        $service = m::mock(Service::class);
        $logger = m::mock(LoggerInterface::class);
        $userValueObject = $this->instance(User::class, m::mock(User::class));
        $controller = new StoreUsersController();

        // Expectations
        $request->expects()
            ->getUserValueObject()
            ->andReturn($userValueObject);

        $service->expects()
            ->handle($userValueObject)
            ->andReturn(['id' => 1, 'name' => 'Some Random Name']);

        // Actions
        $result = $controller->store($request, $service, $logger);

        // Assertions
        $this->assertSame(Response::HTTP_CREATED, $result->getStatusCode());
        $this->assertSame(
            '{"message":"Success","data":{"user":{"id":1,"name":"Some Random Name"}}}',
            $result->getContent()
        );
    }

    /**
     * @dataProvider getStoreExceptionsCases
     */
    public function test_should_throw_an_exception(
        Exception $exception,
        string $loggerMethod,
        string $loggerMessage,
        int $expectedResponseStatusCode,
        string $expectedResponseContent
    ): void {
        // Set
        $request = m::mock(UserRequest::class);
        $service = m::mock(Service::class);
        $logger = m::mock(LoggerInterface::class);
        $userValueObject = $this->instance(User::class, m::mock(User::class));
        $controller = new StoreUsersController();

        // Expectations
        $request->expects()
            ->getUserValueObject()
            ->andReturn($userValueObject);

        $service->expects()
            ->handle($userValueObject)
            ->andThrow($exception);

        $logger->expects()
            ->{$loggerMethod}($loggerMessage, ['exception' => $exception]);

        // Actions
        $result = $controller->store($request, $service, $logger);

        // Assertions
        $this->assertSame($expectedResponseStatusCode, $result->getStatusCode());
        $this->assertSame($expectedResponseContent, $result->getContent());
    }

    public function getStoreExceptionsCases(): array
    {
        return [
            'user exception when failed storing' => [
                'exception' => UserException::failedStoring(),
                'loggerMethod' => 'notice',
                'loggerMessage' => 'Something went wrong while storing the user',
                'expectedResponseStatusCode' => Response::HTTP_SERVICE_UNAVAILABLE,
                'expectedResponseContent' => '{"message":"The new user cannot be stored.","data":[]}',
            ],
            'user exception when email already exists' => [
                'exception' => UserException::emailAlreadyExists(),
                'loggerMethod' => 'notice',
                'loggerMessage' => 'Something went wrong while storing the user',
                'expectedResponseStatusCode' => Response::HTTP_CONFLICT,
                'expectedResponseContent' => '{"message":"The email has already been taken.","data":[]}',
            ],
            'user exception when fiscal doc already exists' => [
                'exception' => UserException::fiscalDocAlreadyExists(),
                'loggerMethod' => 'notice',
                'loggerMessage' => 'Something went wrong while storing the user',
                'expectedResponseStatusCode' => Response::HTTP_CONFLICT,
                'expectedResponseContent' => '{"message":"The fiscal doc has already been taken.","data":[]}',
            ],
            'generic exception for a non expected error' => [
                'exception' => new Exception('Some unexpected random error occurs.'),
                'loggerMethod' => 'warning',
                'loggerMessage' => 'Something went wrong.',
                'expectedResponseStatusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'expectedResponseContent' => '{"message":"Error","data":[]}',
            ],
        ];
    }

    /**
     * @dataProvider getLoginExceptionCases
     */
    public function test_should_throw_an_exception_when_trying_to_login(
        Exception $exception,
        string $loggerMethod,
        string $loggerMessage,
        int $expectedResponseStatusCode,
        string $expectedResponseContent
    ): void {
        // Set
        $request = m::mock(LoginRequest::class);
        $service = m::mock(LoginService::class);
        $logger = m::mock(LoggerInterface::class);
        $controller = new StoreUsersController();
        $requestData = [
            'email' => 'random@email.com',
            'password' => 'secret',
        ];

        // Expectations
        $request->expects()
            ->all()
            ->andReturn($requestData);

        $service->expects()
            ->handle($requestData)
            ->andThrow($exception);

        $logger->expects()
            ->{$loggerMethod}($loggerMessage, ['exception' => $exception]);

        // Actions
        $result = $controller->login($request, $service, $logger);

        // Assertions
        $this->assertSame($expectedResponseStatusCode, $result->getStatusCode());
        $this->assertSame($expectedResponseContent, $result->getContent());
    }

    public function getLoginExceptionCases(): array
    {
        return [
            'cannot login when the given data is invalid' => [
                'exception' => LoginException::invalidData(),
                'loggerMethod' => 'notice',
                'loggerMessage' => 'Something went wrong while logging in.',
                'expectedResponseStatusCode' => Response::HTTP_UNAUTHORIZED,
                'expectedResponseContent' => '{"message":"The given data is invalid.","data":[]}',
            ],
            'cannot login when the user was not found' => [
                'exception' => LoginException::userNotFound(),
                'loggerMethod' => 'notice',
                'loggerMessage' => 'Something went wrong while logging in.',
                'expectedResponseStatusCode' => Response::HTTP_MOVED_PERMANENTLY,
                'expectedResponseContent' => '{"message":"User not found.","data":[]}',
            ],
            'user exception when fiscal doc already exists' => [
                'exception' => new Exception('Some unexpected random error occurs.'),
                'loggerMethod' => 'warning',
                'loggerMessage' => 'Something went wrong.',
                'expectedResponseStatusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'expectedResponseContent' => '{"message":"Error","data":[]}',
            ],
        ];
    }

    public function test_should_proceed_with_the_login(): void
    {
        // Set
        $request = m::mock(LoginRequest::class);
        $service = m::mock(LoginService::class);
        $logger = m::mock(LoggerInterface::class);
        $controller = new StoreUsersController();
        $requestData = [
            'email' => 'random@email.com',
            'password' => 'secret',
        ];

        // Expectations
        $request->expects()
            ->all()
            ->andReturn($requestData);

        $service->expects()
            ->handle($requestData)
            ->andReturn([
                'message' => 'You\'re logged in!',
                'data' => [
                    'token' => 'your_access_token',
                ],
            ]);

        // Actions
        $result = $controller->login($request, $service, $logger);

        // Assertions
        $this->assertSame(Response::HTTP_ACCEPTED, $result->getStatusCode());
        $this->assertSame(
            '{"message":"You\'re logged in!","data":{"token":"your_access_token"}}',
            $result->getContent()
        );
    }
}
