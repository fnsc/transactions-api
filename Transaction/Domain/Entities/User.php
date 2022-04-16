<?php

namespace Transaction\Domain\Entities;

use Transaction\Domain\ValueObjects\Email;
use Transaction\Domain\ValueObjects\Password;

class User
{
    private Account $account;

    public static function newUser(
        int $id,
        string $name = '',
        string $email = '',
        string $registrationNumber = '',
        string $type = '',
        string $password = ''
    ): self
    {
        $email = new Email($email);
        $password = app(Password::class, ['password' => $password]);

        return new static($id, $name, $registrationNumber, $email, $password, $type);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getAccount(): ?Account
    {
        return $this->account ?? null;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    private function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $registrationNumber,
        private readonly Email $email,
        private readonly Password $password,
        private readonly string $type
    ) {
    }
}
