<?php

namespace Transfer\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use Transfer\FraudException;
use Transfer\Http\Requests\TransferRequest;
use Transfer\Store\Service;
use Transfer\TransferException;

class TransfersController extends Controller
{
    public function store(TransferRequest $request, Service $service, LoggerInterface $logger): JsonResponse
    {
        try {
            $result = $service->handle($request->getTransferData(), $request->getAuthenticatedUser());

            return response()->json($result, Response::HTTP_ACCEPTED);
        } catch (FraudException $exception) {
            $logger->alert('Maybe something nasty is happening.', compact('exception'));

            return response()->json([
                'message' => $exception->getMessage(),
                'data' => [],
            ], $exception->getCode());
        } catch (TransferException $exception) {
            $logger->notice(
                'Something went wrong while we transferring the solicited amount.',
                compact('exception')
            );

            return response()->json([
                'message' => $exception->getMessage(),
                'data' => [],
            ], $exception->getCode());
        } catch (Exception $exception) {
            $logger->warning('Something unexpected happened.', compact('exception'));

            return response()->json([
                'message' => 'Error',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function forbidden(): JsonResponse
    {
        return response()->json([
            'message' => 'This user cannot proceed with this transaction.',
            'data' => [],
        ], Response::HTTP_FORBIDDEN);
    }

    public function unauthorized(): JsonResponse
    {
        return response()->json([
            'message' => 'You must be logged in to proceed with a transaction.',
            'data' => [],
        ], Response::HTTP_UNAUTHORIZED);
    }
}
