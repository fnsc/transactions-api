<?php

namespace Transaction\Infra\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Transaction\Application\Exceptions\LoginException;
use Transaction\Application\Login\InputBoundary;
use Transaction\Application\Login\OutputBoundary;
use Transaction\Application\Login\Service as LoginService;
use Transaction\Domain\Entities\User as UserEntity;
use Transaction\Infra\Http\Requests\LoginRequest;
use Transaction\Infra\Presenters\UserTransformer;

class LoginControllerTest extends TestCase
{
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
        $service = $this->createMock(LoginService::class);
        $transformer = m::mock(UserTransformer::class);
        $logger = m::mock(LoggerInterface::class);
        $controller = new LoginController($service, $transformer, $logger);

        $input = new InputBoundary(
        'user@email.com',
        'secret',
        );

        // Expectations
        $request->expects()
            ->get('email')
            ->andReturn('user@email.com');

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
        $result = $controller->login($request);

        // Assertions
        $this->assertSame($expectedResponseStatusCode, $result->getStatusCode());
        $this->assertSame($expectedResponseContent, $result->getContent());
    }

    public function test_should_proceed_with_the_login(): void
    {
        // Set
        $request = m::mock(LoginRequest::class);
        $service = $this->createMock(LoginService::class);
        $transformer = m::mock(UserTransformer::class);
        $logger = m::mock(LoggerInterface::class);
        $controller = new LoginController($service, $transformer, $logger);

        $input = new InputBoundary('user@email.com', 'secret');
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
            ->get('email')
            ->andReturn('user@email.com');

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
        $result = $controller->login($request);

        // Assertions
        $this->assertSame(Response::HTTP_ACCEPTED, $result->getStatusCode());
        $this->assertSame(json_encode($expected), $result->getContent());
    }

    public function getLoginExceptionCases(): array
    {
        return [
            'cannot login when the given data is invalid' => [
                'exception' => LoginException::invalidData(),
                'loggerMethod' => 'notice',
                'loggerMessage' => 'Something went wrong while logging in.',
                'expectedResponseStatusCode' => Response::HTTP_FORBIDDEN,
                'expectedResponseContent' => '{"message":"The given data is invalid.","data":[]}',
            ],
            'cannot login when the user was not found' => [
                'exception' => LoginException::userNotFound(),
                'loggerMethod' => 'notice',
                'loggerMessage' => 'Something went wrong while logging in.',
                'expectedResponseStatusCode' => Response::HTTP_FORBIDDEN,
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
}
