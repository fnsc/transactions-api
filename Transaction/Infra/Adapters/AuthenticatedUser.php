<?php

namespace Transaction\Infra\Adapters;

use Illuminate\Support\Facades\Auth;
use Transaction\Application\Contracts\AuthenticatedUserAdapter as AuthenticatedUserAdapterInterface;
use Transaction\Domain\Entities\User;

class AuthenticatedUser implements AuthenticatedUserAdapterInterface
{
    public function getAuthenticatedUser(): User
    {
        $user = Auth::user();

        return User::newUser(
            $user->id,
            $user->name,
            $user->email,
            $user->registration_number,
            $user->type
        );
    }
}
