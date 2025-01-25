<?php

namespace App\Services;

use App\Dto\CheckBalanceInput;
use App\Dto\ConfirmPaymentInput;
use App\Dto\PaymentInput;
use App\Dto\RechargeWalletInput;
use App\Dto\ResponseDto;
use App\Dto\CreateClientInput;
use App\Entities\Client;
use App\Entities\Wallet;
use App\Entities\Transaction;
use App\Entities\PaymentSession;
use App\Utils\DtoMapper;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Str;
use DateTime;

class WalletSoapService
{
    private EntityManagerInterface $em;
    private Auth0Service $auth0Service;

    public function __construct(EntityManagerInterface $em, Auth0Service $auth0Service)
    {
        $this->em = $em;
        $this->auth0Service = $auth0Service;
    }

    /**
     * Registro de cliente (método público, sin auth)
     * @param mixed $createClientInput
     * @return ResponseDto
     */
    public function createClient(mixed $createClientInput)
    {
        if ($createClientInput instanceof \stdClass) {
            $createClientInput = DtoMapper::mapToDto($createClientInput, CreateClientInput::class);
        }
        if (!$createClientInput instanceof CreateClientInput) {
            return new ResponseDto(false, '01', 'Datos de entrada no válidos', []);
        }

        // Verificar si ya existe el cliente
        $existingClient = $this->em->getRepository(Client::class)->findOneBy(['document' => $createClientInput->getDocument()]);
        if ($existingClient) {
            return new ResponseDto(false, '02', 'El cliente ya existe', []);
        }

        // Crear usuario en Auth0
        $auth0User = $this->auth0Service->createUser($createClientInput);
        if (!$auth0User) {
            return new ResponseDto(false, '03', 'Error al crear usuario en Auth0', []);
        }
        $auth0UserId = $auth0User['user_id'];
        $formattedAuth0UserId = explode('|', $auth0UserId)[1] ?? null;

        // Crear cliente local
        $client = (new Client())
            ->setDocument($createClientInput->getDocument())
            ->setNames($createClientInput->getNames())
            ->setEmail($createClientInput->getEmail())
            ->setCellphone($createClientInput->getCellphone())
            ->setAuth0Id($formattedAuth0UserId);

        // Crear su billetera
        $wallet = (new Wallet())
            ->setBalance(0.0)
            ->setClient($client);

        $client->setWallet($wallet);

        $this->em->persist($client);
        $this->em->persist($wallet);
        $this->em->flush();

        return new ResponseDto(true, '00', 'Cliente creado exitosamente', [
            'client_id' => $client->getId(),
            'auth0_user_id' => $auth0User['user_id'] ?? null
        ]);
    }

    /**
     * Recarga Billetera
     * @param mixed $rechargeWalletInput
     * @return ResponseDto
     */
    public function rechargeWallet(mixed $rechargeWalletInput): ResponseDto
    {
        if ($rechargeWalletInput instanceof \stdClass) {
            $rechargeWalletInput = DtoMapper::mapToDto($rechargeWalletInput, RechargeWalletInput::class);
        }
        if (!$rechargeWalletInput instanceof RechargeWalletInput) {
            return new ResponseDto(false, '01', 'Datos de entrada no válidos', []);
        }

        // Buscar cliente
        $client = $this->em->getRepository(Client::class)->findOneBy([
            'document' => $rechargeWalletInput->getDocument(),
            'cellphone' => $rechargeWalletInput->getCellphone()
        ]);

        if (!$client) {
            return new ResponseDto(false, '04', 'Cliente no encontrado', []);
        }

        // Actualizar balance
        $wallet = $client->getWallet();
        if (!$wallet) {
            return new ResponseDto(false, '05', 'El cliente no tiene billetera', []);
        }

        $wallet->setBalance($wallet->getBalance() + $rechargeWalletInput->getAmount());

        // Registrar transacción
        $transaction = (new Transaction())
            ->setClient($client)
            ->setAmount($rechargeWalletInput->getAmount())
            ->setType(Transaction::TYPE_RECHARGE)
            ->setStatus(Transaction::STATUS_SUCCESS);

        $this->em->persist($wallet);
        $this->em->persist($transaction);
        $this->em->flush();

        return new ResponseDto(true, '00', 'Recarga exitosa', [
            'new_balance' => $wallet->getBalance()
        ]);
    }

    /**
     * Iniciar pago (genera un token y un sessionId)
     * @param mixed $paymentInput
     * @return ResponseDto
     */
    public function pay(mixed $paymentInput): ResponseDto
    {
        if ($paymentInput instanceof \stdClass) {
            $paymentInput = DtoMapper::mapToDto($paymentInput, PaymentInput::class);
        }
        if (!$paymentInput instanceof PaymentInput) {
            return new ResponseDto(false, '01', 'Datos de entrada no válidos', []);
        }

        $client = $this->em->getRepository(Client::class)->findOneBy([
            'document' => $paymentInput->getDocument(),
            'cellphone' => $paymentInput->getCellphone()
        ]);

        if (!$client) {
            return new ResponseDto(false, '04', 'Cliente no encontrado', []);
        }

        $wallet = $client->getWallet();
        if (!$wallet) {
            return new ResponseDto(false, '05', 'El cliente no tiene billetera', []);
        }

        // Validar si tiene saldo suficiente
        if ($wallet->getBalance() < $paymentInput->getAmount()) {
            return new ResponseDto(false, '06', 'Saldo insuficiente', []);
        }

        // Generar token de 6 dígitos
        // aca el token se puede generar de con cache y ponerle un tiempo de expiracion ahi para no guardarlo en base de datos
        $token = random_int(100000, 999999);
        $sessionId = (string)Str::uuid();

        // Guardar PaymentSession
        $paymentSession = new PaymentSession();
        $paymentSession->setSessionId($sessionId)
            ->setToken((string)$token)
            ->setClient($client)
            ->setAmount($paymentInput->getAmount())
            ->setCreatedAt(new DateTime())
            ->setTokenExpiresAt((new DateTime())->modify('+15 minutes'));
        $this->em->persist($paymentSession);
        $this->em->flush();

        // Aquí Se enviaria el token por correo depende de la implementación que se use, en este caso se envia
        // en la api rest por eso retornamos el token en la respuesta

        return new ResponseDto(true, '00', 'Se ha enviado el token al correo. Debes confirmar la compra.', [
            'session_id' => $sessionId,
            'token_debug' => $token
        ]);
    }

    /**
     * Confirmar Pago
     * @param mixed $confirmPaymentInput
     * @return ResponseDto
     */
    public function confirmPayment($confirmPaymentInput): ResponseDto
    {
        if ($confirmPaymentInput instanceof \stdClass) {
            $confirmPaymentInput = DtoMapper::mapToDto($confirmPaymentInput, ConfirmPaymentInput::class);
        }
        if (!$confirmPaymentInput instanceof ConfirmPaymentInput) {
            return new ResponseDto(false, '01', 'Datos de entrada no válidos', []);
        }

        $paymentSession = $this->em->getRepository(PaymentSession::class)->findOneBy([
            'sessionId' => $confirmPaymentInput->getSessionId()
        ]);

        if (!$paymentSession) {
            return new ResponseDto(false, '07', 'Sesión de pago no encontrada', []);
        }

        if ($paymentSession->isConfirmed()) {
            return new ResponseDto(false, '08', 'El pago ya fue confirmado anteriormente', []);
        }

        if ($paymentSession->getToken() !== $confirmPaymentInput->getToken()) {
            return new ResponseDto(false, '09', 'Token inválido', []);
        }

        if($paymentSession->getTokenExpiresAt() < new DateTime()){
            return new ResponseDto(false, '10', 'El token ha expirado', []);
        }

        // Marcar como confirmado
        $paymentSession->setConfirmed(true);

        // Descontar del saldo
        $client = $paymentSession->getClient();
        $wallet = $client->getWallet();
        if ($wallet->getBalance() < $paymentSession->getAmount()) {
            return new ResponseDto(false, '06', 'Saldo insuficiente', []);
        }

        $wallet->setBalance($wallet->getBalance() - $paymentSession->getAmount());

        // Crear transacción
        $transaction = (new Transaction())
            ->setClient($client)
            ->setAmount($paymentSession->getAmount())
            ->setType(Transaction::TYPE_PAYMENT)
            ->setStatus(Transaction::STATUS_SUCCESS);

        $this->em->persist($paymentSession);
        $this->em->persist($wallet);
        $this->em->persist($transaction);
        $this->em->flush();

        return new ResponseDto(true, '00', 'Pago confirmado con éxito', [
            'new_balance' => $wallet->getBalance()
        ]);
    }

    /**
     * Consultar saldo
     * @param mixed $checkBalanceInput
     * @return ResponseDto
     */
    public function checkBalance($checkBalanceInput): ResponseDto
    {
        if ($checkBalanceInput instanceof \stdClass) {
            $checkBalanceInput = DtoMapper::mapToDto($checkBalanceInput, CheckBalanceInput::class);
        }

        if (!$checkBalanceInput instanceof CheckBalanceInput) {
            return new ResponseDto(false, '01', 'Datos de entrada no válidos', []);
        }

        $client = $this->em->getRepository(Client::class)->findOneBy([
            'document' => $checkBalanceInput->getDocument(),
            'cellphone' => $checkBalanceInput->getCellphone()
        ]);

        if (!$client) {
            return new ResponseDto(false, '04', 'Cliente no encontrado', []);
        }

        $wallet = $client->getWallet();
        if (!$wallet) {
            return new ResponseDto(false, '05', 'El cliente no tiene billetera', []);
        }

        return new ResponseDto(true, '00', 'Consulta de saldo exitosa', [
            'balance' => $wallet->getBalance()
        ]);
    }
}
