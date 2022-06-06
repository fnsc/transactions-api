<?php

namespace Transaction\Infra\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Transaction\Application\Exceptions\FraudException;
use Transaction\Application\Exceptions\TransferException;
use Transaction\Application\StoreTransaction\InputBoundary;
use Transaction\Application\StoreTransaction\OutputBoundary;
use Transaction\Application\StoreTransaction\Service;
use Transaction\Domain\Entities\Transaction as TransactionEntity;
use Transaction\Infra\Http\Requests\TransactionRequest;
use Transaction\Infra\Transformers\Transaction;

class TransactionsControllerTest extends TestCase
{
    public function testShouldStoreATransferSuccessfully(): void
    {
        // Set
        $request = m::mock(TransactionRequest::class);
        $service = $this->createMock(Service::class);
        $logger = m::mock(LoggerInterface::class);
        $transformer = m::mock(Transaction::class);
        $controller = new TransactionsController(
            $service,
            $logger,
            $transformer
        );

        $transaction = m::mock(TransactionEntity::class);
        $input = new InputBoundary(1, 2, '100.97');
        $output = new OutputBoundary($transaction);

        $expected = [
            'message' => 'Success!!!',
            'data' => [
                'transaction' => [
                    'payer' => 'Payer Name',
                    'payee' => 'Payee Name',
                    'amount' => '100.97',
                ],
            ],
        ];

        // Expectations
        $request->expects()
            ->get('payee_id')
            ->andReturn('1');

        $request->expects()
            ->get('payer_id')
            ->andReturn('2');

        $request->expects()
            ->get('amount')
            ->andReturn('100.97');

        $service->expects($this->once())
            ->method('handle')
            ->with($input)
            ->willReturn($output);

        $transformer->expects()
            ->transform($transaction)
            ->andReturn([
                'payer' => 'Payer Name',
                'payee' => 'Payee Name',
                'amount' => '100.97',
            ]);

        // Actions
        $result = $controller->store($request);

        // Assertions
        $this->assertSame(Response::HTTP_ACCEPTED, $result->getStatusCode());
        $this->assertSame(json_encode($expected), $result->getContent());
    }

    /**
     * @dataProvider getExceptionsScenarios
     */
    public function testShouldThrowAnExceptionWhenSomethingGoesWrong(
        Exception $exception,
        string $exceptionMessage,
        int $expectedStatusCode,
        string $loggerMethod,
        string $loggerMessage
    ): void {
        // Set
        $request = m::mock(TransactionRequest::class);
        $service = $this->createMock(Service::class);
        $logger = m::mock(LoggerInterface::class);
        $transformer = m::mock(Transaction::class);
        $controller = new TransactionsController(
            $service,
            $logger,
            $transformer
        );
        $expected = [
            'message' => $exceptionMessage,
            'data' => [],
        ];
        $input = new InputBoundary(1, 2, '100.97');

        // Expectations
        $request->expects()
            ->get('payee_id')
            ->andReturn('1');

        $request->expects()
            ->get('payer_id')
            ->andReturn('2');

        $request->expects()
            ->get('amount')
            ->andReturn('100.97');

        $service->expects($this->once())
            ->method('handle')
            ->with($input)
            ->willThrowException($exception);

        $logger->expects()
            ->{$loggerMethod}($loggerMessage, ['exception' => $exception]);

        // Actions
        $result = $controller->store($request);

        // Assertions
        $this->assertSame($expectedStatusCode, $result->getStatusCode());
        $this->assertSame(json_encode($expected), $result->getContent());
    }

    public function getExceptionsScenarios(): array
    {
        return [
            'fraud exception' => [
                'exception' => FraudException::payerIdIsDifferent(),
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
                'exception' => new Exception('Random error'),
                'exceptionMessage' => 'Error',
                'expectedStatusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'loggerMethod' => 'warning',
                'loggerMessage' => 'Something unexpected happened.',
            ],
        ];
    }
}
