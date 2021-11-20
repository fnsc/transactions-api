<?php

namespace Transfer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use User\EnumUserType;

class SendTransferMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = auth()->user();

        if (EnumUserType::REGULAR !== $user->type) {
            return abort(Response::HTTP_FORBIDDEN, 'This user cannot do a transfer.');
        }

        return $next($request);
    }
}
