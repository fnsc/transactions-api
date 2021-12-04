<?php

namespace Transfer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use User\EnumUserType;

class SendTransferMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = auth()->user();

        if (EnumUserType::REGULAR === $user->type) {
            return $next($request);
        }

        return redirect(route('api.v1.transfers.forbidden'));
    }
}
