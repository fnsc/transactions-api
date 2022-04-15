<?php

namespace User\Store;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User
{
    /**
     * @var string|mixed
     */
    private string $name;

    /**
     * @var string|mixed
     */
    private string $email;

    /**
     * @var string|mixed
     */
    private string $password;

    /**
     * @var string|mixed
     */
    private string $registrationNumber;

    /**
     * @param string
     */
    private string $type;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->registrationNumber = $data['registration_number'];
        $this->type = $data['type'];
    }

    public function getName(): string
    {
        return Str::headline($this->name);
    }

    public function getEmail(): string
    {
        return Str::lower($this->email);
    }

    public function getPassword(): string
    {
        return Hash::make($this->password);
    }

    public function getRegistrationNumber(): string
    {
        return preg_replace('/[^0-9]/is', '', $this->registrationNumber);
    }

    public function getType(): string
    {
        return Str::lower($this->type);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'registration_number' => $this->getRegistrationNumber(),
            'type' => $this->getType(),
        ];
    }
}
