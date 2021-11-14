<?php

namespace User\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Mockery as m;
use Psr\Log\LoggerInterface;
use Tests\TestCase;
use User\Http\Requests\UserRequest;
use User\Store\Service;
use User\Store\User;
use User\UserException;

class UsersControllerTest extends TestCase
{
    public function test_should_save_the_new_user(): void
    {
        // Set
        $request = m::mock(UserRequest::class);
        $service = m::mock(Service::class);
        $logger = m::mock(LoggerInterface::class);
        $userValueObject = $this->instance(User::class, m::mock(User::class));
        $controller = new UsersController();

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
     * @dataProvider getExceptionsCases
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
        $controller = new UsersController();

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

    public function getExceptionsCases(): array
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
}
