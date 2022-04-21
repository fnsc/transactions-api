<?php

namespace Transaction\Infra\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Transaction\Application\Exceptions\UserException;
use Transaction\Application\Login\OutputBoundary;
use Transaction\Application\StoreUser\InputBoundary;
use Transaction\Application\StoreUser\Service;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Http\Requests\UserRequest;
use Transaction\Infra\Transformers\User;

class StoreUsersControllerTest extends TestCase
{
    public function test_should_save_the_new_user(): void
    {
        // Set
        $request = m::mock(UserRequest::class);
        $service = $this->createMock(Service::class);
        $transformer = m::mock(User::class);
        $logger = m::mock(LoggerInterface::class);
        $controller = new StoreUsersController($service, $transformer, $logger);

        $input = new InputBoundary(
        'User Name',
        'user@email.com',
        '12345678909',
        'regular',
        'secret',
        );
        $user = m::mock(UserEntity::class);
        $output = new OutputBoundary($user);

        $expected = [
            'message' => 'Success!!!',
            'data' => [
                'user' => [
                    'name' => 'User Name',
                    'auth' => [
                        'token' => 'auth token',
                    ],
                ],
            ],
        ];

        // Expectations
        $request->expects()
            ->get('name')
            ->andReturn('User Name');

        $request->expects()
            ->get('email')
            ->andReturn('user@email.com');

        $request->expects()
            ->get('registration_number')
            ->andReturn('12345678909');

        $request->expects()
            ->get('type')
            ->andReturn('regular');

        $request->expects()
            ->get('password')
            ->andReturn('secret');

        $service->expects($this->once())
            ->method('handle')
            ->with($input)
            ->willReturn($output);

        $transformer->expects()
            ->transform($user)
            ->andReturn([
                'name' => 'User Name',
                'auth' => [
                    'token' => 'auth token',
                ],
            ]);

        // Actions
        $result = $controller->store($request);

        // Assertions
        $this->assertSame(Response::HTTP_CREATED, $result->getStatusCode());
        $this->assertSame(json_encode($expected), $result->getContent());
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
        $service = $this->createMock(Service::class);
        $transformer = m::mock(User::class);
        $logger = m::mock(LoggerInterface::class);
        $controller = new StoreUsersController($service, $transformer, $logger);

        $input = new InputBoundary(
        'User Name',
        'user@email.com',
        '12345678909',
        'regular',
        'secret',
        );

        // Expectations
        $request->expects()
            ->get('name')
            ->andReturn('User Name');

        $request->expects()
            ->get('email')
            ->andReturn('user@email.com');

        $request->expects()
            ->get('registration_number')
            ->andReturn('12345678909');

        $request->expects()
            ->get('type')
            ->andReturn('regular');

        $request->expects()
            ->get('password')
            ->andReturn('secret');

        $service->expects($this->once())
            ->method('handle')
            ->with($input)
            ->willThrowException($exception);

        $logger->expects()
            ->{$loggerMethod}($loggerMessage, ['exception' => $exception]);

        // Actions
        $result = $controller->store($request);

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
                'expectedResponseStatusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedResponseContent' => '{"message":"The new user cannot be stored.","data":[]}',
            ],
            'user exception when email already exists' => [
                'exception' => UserException::emailAlreadyExists(),
                'loggerMethod' => 'notice',
                'loggerMessage' => 'Something went wrong while storing the user',
                'expectedResponseStatusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedResponseContent' => '{"message":"The email has already been taken.","data":[]}',
            ],
            'user exception when fiscal doc already exists' => [
                'exception' => UserException::fiscalDocAlreadyExists(),
                'loggerMethod' => 'notice',
                'loggerMessage' => 'Something went wrong while storing the user',
                'expectedResponseStatusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
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
