<?php

namespace User\Login;

use Transaction\Infra\Eloquent\User;

class TokenManager
{
    public function manage(User $user): string
    {
        $this->deleteTokens($user);

        return $user->createToken('auth')->plainTextToken;
    }

    private function deleteTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}
