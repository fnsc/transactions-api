<?php

namespace User\Store;

use Transaction\Infra\Eloquent\User;

class Transformer
{
    public function transform(User $user, string $token): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'type' => $user->type,
            'account' => [
                'number' => $user->account->number,
            ],
            'token' => $token,
        ];
    }
}
