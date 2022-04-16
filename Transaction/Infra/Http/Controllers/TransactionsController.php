<?php

namespace Transaction\Infra\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use Transaction\Application\StoreTransaction\FraudException;
use Transaction\Application\StoreTransaction\InputBoundary;
use Transaction\Application\StoreTransaction\Service;
use Transaction\Infra\Http\Requests\TransferRequest;
use Transaction\Infra\Presenters\TransactionTransformer;
use Transaction\TransferException;

class TransactionsController extends Controller
{
    public function __construct(
        private readonly Service $service,
        private readonly LoggerInterface $logger,
        private readonly TransactionTransformer $transformer
    ) {
    }

    public function store(TransferRequest $request): JsonResponse
    {
        try {
            $input = $this->getInputBoundary($request);
            $result = $this->service->handle($input);
            $result = $this->transformer->transform($result->getTransaction());

            return new JsonResponse([
                'message' => 'Success!!!',
                'data' => [
                    'transaction' => $result,
                ],
            ], Response::HTTP_ACCEPTED);
        } catch (FraudException $exception) {
            $this->logger->alert('Maybe something nasty is happening.', compact('exception'));

            return new JsonResponse([
                'message' => $exception->getMessage(),
                'data' => [],
            ], Response::HTTP_NOT_ACCEPTABLE);
        } catch (TransferException $exception) {
            $this->logger->notice(
                'Something went wrong while we transferring the solicited amount.',
                compact('exception')
            );

            return new JsonResponse([
                'message' => $exception->getMessage(),
                'data' => [],
            ], Response::HTTP_NOT_ACCEPTABLE);
        } catch (Exception $exception) {
            $this->logger->warning('Something unexpected happened.', compact('exception'));

            return new JsonResponse([
                'message' => 'Error',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function forbidden(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'This user cannot proceed with this transaction.',
            'data' => [],
        ], Response::HTTP_FORBIDDEN);
    }

    public function unauthorized(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'You must be logged in to proceed with a transaction.',
            'data' => [],
        ], Response::HTTP_UNAUTHORIZED);
    }

    private function getInputBoundary(TransferRequest $request): InputBoundary
    {
        return new InputBoundary(
            $request->get('payee_id'),
            $request->get('payer_id'),
            $request->get('amount')
        );
    }
}
