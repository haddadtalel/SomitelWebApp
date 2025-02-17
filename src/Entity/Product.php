<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "product")]
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    // Define ENUM categories as constants
    public const CATEGORY_NETWORKING = 'Networking Equipment';
    public const CATEGORY_TELEPHONY = 'PABX & Telephony';
    public const CATEGORY_FIRE_SAFETY = 'Fire Safety Solutions';
    public const CATEGORY_SECURITY = 'Security & Access Control';
    public const CATEGORY_ACCESSORIES = 'Networking Accessories';
    public const CATEGORY_POWER = 'Power & Electrical Solutions';

    #[ORM\Column(type: "string", length: 255)]
    private string $category;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $model = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private int $stockQuantity = 0;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $imageUrl = null;

    // Removed database default options so Doctrine always provides a value.
    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTime $updatedAt = null;

    public function __construct()
    {
        // Initialize with current date/time.
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
    }

    // --- Getters and Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    
    public function getCategory(): string
    {
        return $this->category;
    }
    public function setCategory(string $category): self
    {
        if (!in_array($category, self::getCategories(), true)) {
            throw new \InvalidArgumentException("Invalid category");
        }
        $this->category = $category;
        return $this;
    }
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_NETWORKING,
            self::CATEGORY_TELEPHONY,
            self::CATEGORY_FIRE_SAFETY,
            self::CATEGORY_SECURITY,
            self::CATEGORY_ACCESSORIES,
            self::CATEGORY_POWER,
        ];
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }
    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }
    public function setModel(?string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getStockQuantity(): int
    {
        return $this->stockQuantity;
    }
    public function setStockQuantity(int $stockQuantity): self
    {
        $this->stockQuantity = $stockQuantity;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }
    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // --- Lifecycle Callbacks ---

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        // If not already set, assign current datetime.
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
        if ($this->updatedAt === null) {
            $this->updatedAt = new \DateTime();
        }
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        // Always update the updatedAt timestamp.
        $this->updatedAt = new \DateTime();
    }
}
