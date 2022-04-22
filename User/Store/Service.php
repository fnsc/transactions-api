<?php

namespace User\Store;

use User\Login\TokenManager;
use User\Repository;

class Service
{
    private Repository $repository;
    private Transformer $transformer;
    private TokenManager $manager;

    public function __construct(Repository $repository, Transformer $transformer, TokenManager $manager)
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
