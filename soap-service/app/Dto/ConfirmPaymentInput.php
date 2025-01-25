<?php

namespace App\Dto;

class ConfirmPaymentInput
{
    private string $sessionId;
    private string $token;

    public function __construct($sessionId, $token)
    {
        $this->sessionId = $sessionId;
        $this->token = $token;
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }
}
