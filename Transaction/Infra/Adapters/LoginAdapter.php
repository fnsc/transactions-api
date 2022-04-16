<?php

namespace Transaction\Infra\Adapters;

use Illuminate\Support\Facades\Auth;
use Transaction\Application\Contracts\LoginAdapter as LoginAdapterInterface;
use Transaction\Domain\Entities\User;

class LoginAdapter implements LoginAdapterInterface
{
    public function attempt(User $user): bool
    {
        return Auth::attempt([
            'email' => $user->getEmail(),
            'password' => $user->getPassword()->getPlainPassword(),
        ]);
    }
}
