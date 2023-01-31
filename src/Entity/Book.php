<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[Groups(['activation'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['activation'])]
    #[ORM\Column(length: 255)]
    private ?string $reference = null;


    #[ORM\Column(nullable: true)]
    private ?bool $isActivated = null;


    #[ORM\Column(nullable: true)]
    private ?bool $isBilled = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $billingDate = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $activationDate = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $modificationdate = null;


    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?Game $game = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $addingDate = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isCheckActivation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isCheckBilling = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?Invoice $invoice = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?Delivery $delivery = null;

    #[ORM\Column(nullable: true)]
    private ?int $repayment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function isIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(?bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    public function isIsBilled(): ?bool
    {
        return $this->isBilled;
    }

    public function setIsBilled(?bool $isBilled): self
    {
        $this->isBilled = $isBilled;

        return $this;
    }

    public function getBillingDate(): ?\DateTimeInterface
    {
        return $this->billingDate;
    }

    public function setBillingDate(?\DateTimeInterface $billingDate): self
    {
        $this->billingDate = $billingDate;

        return $this;
    }

    public function getActivationDate(): ?\DateTimeInterface
    {
        return $this->activationDate;
    }

    public function setActivationDate(?\DateTimeInterface $activationDate): self
    {
        $this->activationDate = $activationDate;

        return $this;
    }

    public function getModificationdate(): ?\DateTimeInterface
    {
        return $this->modificationdate;
    }

    public function setModificationdate(\DateTimeInterface $modificationdate): self
    {
        $this->modificationdate = $modificationdate;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getAddingDate(): ?\DateTimeInterface
    {
        return $this->addingDate;
    }

    public function setAddingDate(\DateTimeInterface $addingDate): self
    {
        $this->addingDate = $addingDate;

        return $this;
    }

    public function isIsCheckActivation(): ?bool
    {
        return $this->isCheckActivation;
    }

    public function setIsCheckActivation(?bool $isCheckActivation): self
    {
        $this->isCheckActivation = $isCheckActivation;

        return $this;
    }

    public function isIsCheckBilling(): ?bool
    {
        return $this->isCheckBilling;
    }

    public function setIsCheckBilling(?bool $isCheckBilling): self
    {
        $this->isCheckBilling = $isCheckBilling;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getRepayment(): ?int
    {
        return $this->repayment;
    }

    public function setRepayment(?int $repayment): self
    {
        $this->repayment = $repayment;

        return $this;
    }



}
