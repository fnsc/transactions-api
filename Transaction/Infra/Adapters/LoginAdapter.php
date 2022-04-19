<?php

namespace Transaction\Infra\Adapters;

use Transaction\Application\Contracts\LoginAdapter as LoginAdapterInterface;
use Transaction\Domain\Contracts\UserRepository;
use Transaction\Domain\Entities\User;

class LoginAdapter implements LoginAdapterInterface
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function attempt(User $user): bool
    {
        $userDatabase = $this->userRepository->getLoginCredentials($user->getEmail());

        if (!$this->isValidEmail($user, $userDatabase)) {
            return false;
        }

        if (!$this->isValidPassword($user, $userDatabase)) {
            return false;
        }

        return true;
    }

    private function isValidPassword(User $user, ?User $userDatabase): bool
    {
        return password_verify(
            $user->getPassword()->getPlainPassword(),
            $userDatabase->getPassword()->getPlainPassword()
        );
    }

    private function isValidEmail(User $user, ?User $userDatabase): bool
    {
        return (string) $userDatabase->getEmail() === (string) $user->getEmail();
    }
}
