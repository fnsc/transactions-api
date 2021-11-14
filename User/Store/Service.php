<?php

namespace User\Store;

use Illuminate\Support\Facades\Auth;
use User\Repository;

class Service
{
    private Repository $repository;
    private Transformer $transformer;

    public function __construct(Repository $repository, Transformer $transformer)
    {
        $this->repository = $repository;
        $this->transformer = $transformer;
    }

    public function handle(User $user): array
    {
        $user = $this->repository->store($user);
        Auth::login($user);

        return $this->transformer->transform($user);
    }
}
