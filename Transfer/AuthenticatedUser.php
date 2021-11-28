<?php

namespace Transfer;

class AuthenticatedUser
{
    private int $id;
    private string $name;
    private string $email;
    private string $type;

    public function __construct()
    {
        $user = auth()->user();

        $this->id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->type = $user->type;
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
}
