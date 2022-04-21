<?php

namespace Transaction\Infra\Transformers;

use Transaction\Domain\Entities\User as UserEntity;

class User
{
    public function transform(UserEntity $user): array
    {
        return [
            'name' => $user->getName(),
            'auth' => [
                'token' => $user->getToken(),
            ],
        ];
    }
}
