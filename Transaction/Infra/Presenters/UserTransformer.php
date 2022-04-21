<?php

namespace Transaction\Infra\Presenters;

use Transaction\Domain\Entities\User;

class UserTransformer
{
    public function transform(User $user): array
    {
        return [
            'name' => $user->getName(),
            'auth' => [
                'token' => $user->getToken(),
            ],
        ];
    }
}
