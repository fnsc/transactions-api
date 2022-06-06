<?php

namespace Transaction\Infra\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ServerErrorResponse
{
    private function getServerErrorResponse(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Error',
            'data' => [],
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
