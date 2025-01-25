<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="wallets")
 */
class Wallet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="float")
     */
    private float $balance;

    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Client", inversedBy="wallet")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private Client $client;

    public function getId(): int
    {
        return $this->id;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;
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
}
