<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\Table(name="clients")
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private string $document; // Documento

    /**
     * @ORM\Column(type="string")
     */
    private string $names; // Nombres

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string")
     */
    private string $cellphone; // Celular

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $auth0Id = null;

    // Relación OneToOne con la billetera
    /**
     * @ORM\OneToOne(targetEntity="App\Entities\Wallet", mappedBy="client", cascade={"persist", "remove"})
     */
    private ?Wallet $wallet = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\Transaction", mappedBy="client", cascade={"persist", "remove"})
     */
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    // Getters y Setters ...

    public function getId(): int
    {
        return $this->id;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function setDocument(string $document): self
    {
        $this->document = $document;
        return $this;
    }

    public function getNames(): string
    {
        return $this->names;
    }

    public function setNames(string $names): self
    {
        $this->names = $names;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCellphone(): string
    {
        return $this->cellphone;
    }

    public function setCellphone(string $cellphone): self
    {
        $this->cellphone = $cellphone;
        return $this;
    }

    public function getAuth0Id(): ?string
    {
        return $this->auth0Id;
    }

    public function setAuth0Id(?string $auth0Id): self
    {
        $this->auth0Id = $auth0Id;
        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): self
    {
        $this->wallet = $wallet;
        return $this;
    }

    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setClient($this);
        }
        return $this;
    }
}
