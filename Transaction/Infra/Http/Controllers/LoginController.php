<?php

namespace Transaction\Infra\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use Transaction\Application\Exceptions\LoginException;
use Transaction\Application\Login\InputBoundary;
use Transaction\Application\Login\Service;
use Transaction\Infra\Http\Requests\LoginRequest;
use Transaction\Infra\Presenters\UserTransformer;

class LoginController extends Controller
{
    public function __construct(
        private readonly Service $service,
        private readonly UserTransformer $transformer,
        private readonly LoggerInterface $logger
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $input = new InputBoundary(
                email: $request->get('email'),
                password: $request->get('password')
            );

            $output = $this->service->handle($input);
            $result = $this->transformer->transform($output->getUser());

            return response()->json([
                'message' => 'Success!!!',
                'data' => [
                    'user' => $result,
                ],
            ], Response::HTTP_ACCEPTED);
        } catch (LoginException $exception) {
            $this->logger->notice('Something went wrong while logging in.', compact('exception'));

            return response()->json([
                'message' => $exception->getMessage(),
                'data' => [],
            ], Response::HTTP_FORBIDDEN);
        } catch (Exception $exception) {
            $this->logger->warning('Something went wrong.', compact('exception'));

            return response()->json([
                'message' => 'Error',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
