<?php

namespace App\Dto;

class CreateClientInput
{
    private string $document;
    private string $names;
    private string $email;
    private string $cellphone;
    private string $password;

    public function __construct(
        string $document,
        string $names,
        string $email,
        string $cellphone,
        string $password
    ) {
        $this->document = $document;
        $this->names = $names;
        $this->email = $email;
        $this->cellphone = $cellphone;
        $this->password = $password;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function getNames(): string
    {
        return $this->names;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCellphone(): string
    {
        return $this->cellphone;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
