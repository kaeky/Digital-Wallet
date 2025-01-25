<?php

namespace App\Dto;

class CheckBalanceInput
{
    public $document;
    public $cellphone;

    public function __construct($document, $cellphone)
    {
        $this->document = $document;
        $this->cellphone = $cellphone;
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function getCellphone()
    {
        return $this->cellphone;
    }

    public function setDocument($document)
    {
        $this->document = $document;
    }

    public function setCellphone($cellphone)
    {
        $this->cellphone = $cellphone;
    }
}
