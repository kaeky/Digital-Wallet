<?php
namespace App\Services;

class VirtualWalletService
{
    public function registerClient($document, $name, $email, $phone)
    {
        return [
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Cliente registrado exitosamente',
            'data' => [
                'document' => $document,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            ],
        ];
    }
}
