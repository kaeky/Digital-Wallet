<?php

namespace App\Dto;

class RechargeWalletInput
{
    private string $document;
    private string $cellphone;
    private float $amount;

    public function __construct(
        string $document,
        string $cellphone,
        float $amount
    ) {
        $this->document = $document;
        $this->cellphone = $cellphone;
        $this->amount = $amount;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function getCellphone(): string
    {
        return $this->cellphone;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
