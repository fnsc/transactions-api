<?php

namespace User\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use User\Http\Requests\LoginRequest;
use User\Http\Requests\UserRequest;
use User\Login\Service as LoginService;
use User\LoginException;
use User\Store\Service;
use User\UserException;

class UsersController extends Controller
{
    public function store(UserRequest $request, Service $service, LoggerInterface $logger): JsonResponse
    {
        try {
            $user = $service->handle($request->getUserValueObject());

            return response()->json([
                'message' => 'Success',
                'data' => [
                    'user' => $user,
                ],
            ], Response::HTTP_CREATED);
        } catch (UserException $exception) {
            $logger->notice('Something went wrong while storing the user', compact('exception'));

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
}
