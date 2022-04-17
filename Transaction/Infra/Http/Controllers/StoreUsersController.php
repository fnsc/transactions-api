<?php

namespace Transaction\Infra\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use Transaction\Application\Exceptions\LoginException;
use Transaction\Application\Exceptions\UserException;
use Transaction\Application\Login\Service as LoginService;
use Transaction\Application\StoreUser\InputBoundary;
use Transaction\Application\StoreUser\Service;
use Transaction\Infra\Http\Requests\LoginRequest;
use Transaction\Infra\Http\Requests\UserRequest;
use Transaction\Infra\Presenters\UserTransformer;

class StoreUsersController extends Controller
{
    public function __construct(
        private readonly Service $service,
        private readonly UserTransformer $transformer,
        private readonly LoggerInterface $logger
    ) {
    }

    public function store(UserRequest $request): JsonResponse
    {
        try {
            $input = $this->getInputBoundary($request);
            $output = $this->service->handle($input);

            $result = $this->transformer->transform($output->getUser());

            return response()->json([
                'message' => 'Success!!!',
                'data' => [
                    'user' => $result,
                ],
            ], Response::HTTP_CREATED);
        } catch (UserException $exception) {
            $this->logger->notice('Something went wrong while storing the user', compact('exception'));

            return response()->json([
                'message' => $exception->getMessage(),
                'data' => [],
            ], $exception->getCode());
        } catch (Exception $exception) {
            $this->logger->warning('Something went wrong.', compact('exception'));

            return response()->json([
                'message' => 'Error',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(LoginRequest $request, LoginService $service, LoggerInterface $logger): JsonResponse
    {
        try {
            $result = $service->handle($request->all());

            return response()->json([
                'message' => $result['message'],
                'data' => $result['data'],
            ], Response::HTTP_ACCEPTED);
        } catch (LoginException $exception) {
            $logger->notice('Something went wrong while logging in.', compact('exception'));

            return response()->json([
                'message' => $exception->getMessage(),
                'data' => [],
            ], $exception->getCode());
        } catch (Exception $exception) {
            $logger->warning('Something went wrong.', compact('exception'));

            return response()->json([
                'message' => 'Error',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getInputBoundary(UserRequest $request): InputBoundary
    {
        return new InputBoundary(
            $request->get('name'),
            $request->get('email'),
            $request->get('registration_number'),
            $request->get('type'),
            $request->get('password'),
        );
    }
}
