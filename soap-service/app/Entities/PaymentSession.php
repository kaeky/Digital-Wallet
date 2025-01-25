<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment_sessions")
 */
class PaymentSession
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $sessionId;

    /**
     * @ORM\Column(type="string")
     */
    private string $token; // el token de 6 dígitos

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $tokenExpiresAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $confirmed = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private Client $client;

    /**
     * @ORM\Column(type="float")
     */
    private float $amount; // cuánto se va a descontar

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters y Setters ...

    public function getId(): int
    {
        return $this->id;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getTokenExpiresAt(): \DateTime
    {
        return $this->tokenExpiresAt;
    }

    public function setTokenExpiresAt(\DateTime $tokenExpiresAt): self
    {
        $this->tokenExpiresAt = $tokenExpiresAt;
        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;
        return $this;
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
