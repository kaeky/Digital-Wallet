<?php

namespace App\Dto;

class ResponseDto
{
    public $success;
    public $errorCode;
    public $errorMessage;
    public $data;

    public function __construct($success, $errorCode, $errorMessage, $data = [])
    {
        $this->success      = $success;
        $this->errorCode    = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->data         = $data;
    }
}
