<?php

namespace App\Entity;

use App\Repository\QuoteDescRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuoteDescRepository::class)]
class QuoteDesc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $head = null;

    #[ORM\Column]
    private array $quoteItems = [];

    #[ORM\Column(length: 255)]
    private ?string $contactNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $contactTitle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getHead(): ?string
    {
        return $this->head;
    }

    public function setHead(string $head): static
    {
        $this->head = $head;

        return $this;
    }

    public function getQuoteItems(): array
    {
        return $this->quoteItems;
    }

    public function setQuoteItems(array $quoteItems): static
    {
        $this->quoteItems = $quoteItems;

        return $this;
    }

    public function getContactNumber(): ?string
    {
        return $this->contactNumber;
    }

    public function setContactNumber(string $contactNumber): static
    {
        $this->contactNumber = $contactNumber;

        return $this;
    }

    public function getContactTitle(): ?string
    {
        return $this->contactTitle;
    }

    public function setContactTitle(string $contactTitle): static
    {
        $this->contactTitle = $contactTitle;

        return $this;
    }
}
