<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['group'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['group'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['group'])]
    private ?int $ticketNumber = null;

    #[ORM\Column]
    #[Groups(['group'])]
    private ?int $ticketPrice = null;

    #[ORM\Column]
    #[Groups(['group'])]
    private ?bool $isActivated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['group'])]
    private ?\DateTimeInterface $addDate = null;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Book::class)]
    private Collection $books;

    #[ORM\Column]
    #[Groups(['group'])]
    private ?int $totalPrice = null;

    #[ORM\Column]
    #[Groups(['group'])]
    private ?int $codeFdj = null;

    #[ORM\Column(length: 255)]
    private ?string $refDeliveryFdj = null;

    #[ORM\Column(length: 255)]
    private ?string $refBillingFdj = null;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTicketNumber(): ?int
    {
        return $this->ticketNumber;
    }

    public function setTicketNumber(int $ticketNumber): self
    {
        $this->ticketNumber = $ticketNumber;

        return $this;
    }

    public function getTicketPrice(): ?int
    {
        return $this->ticketPrice;
    }

    public function setTicketPrice(int $ticketPrice): self
    {
        $this->ticketPrice = $ticketPrice;

        return $this;
    }

    public function isIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    public function getAddDate(): ?\DateTimeInterface
    {
        return $this->addDate;
    }

    public function setAddDate(\DateTimeInterface $addDate): self
    {
        $this->addDate = $addDate;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setGame($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getGame() === $this) {
                $book->setGame(null);
            }
        }

        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }



    public function getCodeFdj(): ?int
    {
        return $this->codeFdj;
    }

    public function setCodeFdj(int $codeFdj): self
    {
        $this->codeFdj = $codeFdj;

        return $this;
    }

    public function getRefDeliveryFdj(): ?string
    {
        return $this->refDeliveryFdj;
    }

    public function setRefDeliveryFdj(string $refDeliveryFdj): self
    {
        $this->refDeliveryFdj = $refDeliveryFdj;

        return $this;
    }

    public function getRefBillingFdj(): ?string
    {
        return $this->refBillingFdj;
    }

    public function setRefBillingFdj(string $refBillingFdj): self
    {
        $this->refBillingFdj = $refBillingFdj;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName().'-'.$this->getCodeFdj();
    }

}
