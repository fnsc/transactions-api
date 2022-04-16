<?php

namespace Transaction\Infra\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Transaction\Domain\UserType;
use Transaction\Infra\Adapters\AuthenticatedUser;
use function auth;
use function redirect;

class SendTransferMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $authenticatedUser = new AuthenticatedUser();
        $user = $authenticatedUser->getAuthenticatedUser();

        if (UserType::REGULAR->value === $user->getType()) {
            return $next($request);
        }

        return redirect(route('api.v1.transfers.forbidden'));
    }
}
