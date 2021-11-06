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

    private string $password;

    /**
     * @var string|mixed
     */
    private string $fiscalDoc;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->fiscalDoc = $data['fiscal_doc'];
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

    public function getFiscalDoc(): string
    {
        return preg_replace('/[^0-9]/is', '', $this->fiscalDoc);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'fiscal_doc' => $this->getFiscalDoc(),
        ];
    }
}
