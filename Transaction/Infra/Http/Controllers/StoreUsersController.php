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
    use ServerErrorResponse;

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

            return $this->getSuccessResponse($result);
        } catch (UserException $exception) {
            $this->logger->notice('Something went wrong while storing the user', compact('exception'));

            return $this->getUnprocessableEntityResponse($exception);
        } catch (Exception $exception) {
            $this->logger->warning('Something went wrong.', compact('exception'));

            return $this->getServerErrorResponse();
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

    private function getSuccessResponse(array $result): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Success!!!',
            'data' => [
                'user' => $result,
            ],
        ], Response::HTTP_CREATED);
    }

    private function getUnprocessableEntityResponse(UserException|Exception $exception): JsonResponse
    {
        return new JsonResponse([
            'message' => $exception->getMessage(),
            'data' => [],
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
