<?php

namespace Transaction\Domain\Entities;

use Transaction\Domain\ValueObjects\Email;
use Transaction\Domain\ValueObjects\Password;

class User
{
    private Account $account;

    private function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $registrationNumber,
        private readonly Email $email,
        private readonly Password $password,
        private readonly string $type,
        private readonly string $token
    ) {
    }

    public static function newUser(
        int $id = 0,
        string $name = '',
        string $email = '',
        string $registrationNumber = '',
        string $type = '',
        string $password = '',
        string $token = ''
    ): self {
        $email = new Email($email);
        $password = new Password($password);

        return new static($id, $name, $registrationNumber, $email, $password, $type, $token);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getAccount(): ?Account
    {
        return $this->account ?? null;
    }

    public function getRegistrationNumber(): string
    {
        return preg_replace('/[^0-9]/is', '', $this->registrationNumber);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }
}
