<?php

namespace User\Store;

use Transaction\Infra\Repositories\User;
use User\Login\TokenManager;

class Service
{
    private User $repository;
    private Transformer $transformer;
    private TokenManager $manager;

    public function __construct(User $repository, Transformer $transformer, TokenManager $manager)
    {
        $this->repository = $repository;
        $this->transformer = $transformer;
        $this->manager = $manager;
    }

    public function handle(User $user): array
    {
        $user = $this->repository->store($user);
        $token = $this->manager->manage($user);

        return $this->transformer->transform($user, $token);
    }
}
