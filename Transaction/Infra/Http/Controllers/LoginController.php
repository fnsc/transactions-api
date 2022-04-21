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
use Transaction\Infra\Transformers\User as UserTransformer;

class LoginController extends Controller
{
    use ServerErrorResponse;

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

            return $this->getSuccessResponse($result);
        } catch (LoginException $exception) {
            $this->logger->notice('Something went wrong while logging in.', compact('exception'));

            return $this->getForbiddenResponse($exception);
        } catch (Exception $exception) {
            $this->logger->warning('Something went wrong.', compact('exception'));

            return $this->getServerErrorResponse();
        }
    }

    private function getSuccessResponse(array $result): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Success!!!',
            'data' => [
                'user' => $result,
            ],
        ], Response::HTTP_ACCEPTED);
    }

    private function getForbiddenResponse(Exception|LoginException $exception): JsonResponse
    {
        return new JsonResponse([
            'message' => $exception->getMessage(),
            'data' => [],
        ], Response::HTTP_FORBIDDEN);
    }
}
