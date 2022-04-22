<?php

namespace User\Login;

use Illuminate\Support\Facades\Auth;
use User\LoginException;
use User\Repository;
use User\User;

class Service
{
    private TokenManager $manager;
    private Repository $repository;

    public function __construct(TokenManager $manager, Repository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    public function handle(array $data): array
    {
        if (!$this->isValid($data)) {
            throw LoginException::invalidData();
        }

        if (!$user = $this->getUser($data['email'])) {
            throw LoginException::userNotFound();
        }

        $token = $this->manager->manage($user);

        return [
            'message' => 'You\'re logged in!',
            'data' => [
                'token' => $token,
            ],
        ];
    }

    private function isValid(array $data): bool
    {
        return Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
    }

    private function getUser(mixed $email): ?User
    {
        return $this->repository->findByEmail($email);
    }
}
