<?php

namespace Tests\Unit\Transfer\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Mockery as m;
use Psr\Log\LoggerInterface;
use Tests\TestCase;
use Transfer\AuthenticatedUser;
use Transfer\FraudException;
use Transfer\Http\Controllers\TransfersController;
use Transfer\Http\Requests\TransferRequest;
use Transfer\Store\Service;
use Transfer\Store\Transfer;
use Transfer\TransferException;

class TransfersControllerTest extends TestCase
{
    public function test_should_store_a_transfer_successfully(): void
    {
        // Set
        $request = m::mock(TransferRequest::class);
        $service = m::mock(Service::class);
        $logger = m::mock(LoggerInterface::class);
        $controller = new TransfersController();
        $expected = [
            'message' => 'You did it!!!',
            'data' => [],
        ];
        $transfer = m::mock(Transfer::class);
        $authenticatedUser = m::mock(AuthenticatedUser::class);

        // Expectations
        $request->expects()
            ->getTransferData()
            ->andReturn($transfer);

        $request->expects()
            ->getAuthenticatedUser()
            ->andReturn($authenticatedUser);

        $service->expects()
            ->handle($transfer, $authenticatedUser)
            ->andReturn($expected);

        // Actions
        $result = $controller->store($request, $service, $logger);

        // Assertions
        $this->assertSame(Response::HTTP_ACCEPTED, $result->getStatusCode());
        $this->assertSame(json_encode($expected), $result->getContent());
    }

    /**
     * @dataProvider getExceptionsScenarios
     */
    public function test_should_throw_an_exception_when_something_goes_wrong(
        Exception $exception,
        string $exceptionMessage,
        int $expectedStatusCode,
        string $loggerMethod,
        string $loggerMessage
    ): void {
        // Set
        $request = m::mock(TransferRequest::class);
        $service = m::mock(Service::class);
        $logger = m::mock(LoggerInterface::class);
        $controller = new TransfersController();
        $expected = [
            'message' => $exceptionMessage,
            'data' => [],
        ];
        $transfer = m::mock(Transfer::class);
        $authenticatedUser = m::mock(AuthenticatedUser::class);

        // Expectations
        $request->expects()
            ->getTransferData()
            ->andReturn($transfer);

        $request->expects()
            ->getAuthenticatedUser()
            ->andReturn($authenticatedUser);

        $service->expects()
            ->handle($transfer, $authenticatedUser)
            ->andThrow($exception);

        $logger->expects()
            ->{$loggerMethod}($loggerMessage, ['exception' => $exception]);

        // Actions
        $result = $controller->store($request, $service, $logger);

        // Assertions
        $this->assertSame($expectedStatusCode, $result->getStatusCode());
        $this->assertSame(json_encode($expected), $result->getContent());
    }

    public function getExceptionsScenarios(): array
    {
        $exception = new Exception('Random error', Response::HTTP_INTERNAL_SERVER_ERROR);

        return [
            'fraud exception' => [
                'exception' => FraudException::payerIdisDifferent(),
                'exceptionMessage' => 'The payer id is different from the user that is currently authenticated.',
                'expectedStatusCode' => Response::HTTP_NOT_ACCEPTABLE,
                'loggerMethod' => 'alert',
                'loggerMessage' => 'Maybe something nasty is happening.',
            ],
            'transfer exception' => [
                'exception' => TransferException::accountNotFound(),
                'exceptionMessage' => 'The informed account was not found on our registers.',
                'expectedStatusCode' => Response::HTTP_NOT_ACCEPTABLE,
                'loggerMethod' => 'notice',
                'loggerMessage' => 'Something went wrong while we transferring the solicited amount.',
            ],
            'unexpected exception' => [
                'exception' => $exception,
                'exceptionMessage' => 'Error',
                'expectedStatusCode' => $exception->getCode(),
                'loggerMethod' => 'warning',
                'loggerMessage' => 'Something unexpected happened.',
            ],
        ];
    }
}