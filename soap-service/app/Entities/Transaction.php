<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="transactions")
 */
class Transaction
{
    /**
     * Estados de la transacción: exitosa, denegada, pendiente
     */
    public const STATUS_SUCCESS = 'exitosa';
    public const STATUS_DENIED = 'denegada';
    public const STATUS_PENDING = 'pendiente';

    /**
     * Tipos de transacción: recarga, compra
     */
    public const TYPE_RECHARGE = 'recarga';
    public const TYPE_PAYMENT = 'pago';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Client", inversedBy="transactions")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private Client $client;

    /**
     * @ORM\Column(type="float")
     */
    private float $amount;

    /**
     * @ORM\Column(type="string")
     */
    private string $type; // recarga o pago

    /**
     * @ORM\Column(type="string")
     */
    private string $status; // exitosa, denegada, pendiente

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters y Setters...

    public function getId(): int
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
