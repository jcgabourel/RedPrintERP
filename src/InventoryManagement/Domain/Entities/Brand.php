<?php

namespace Src\InventoryManagement\Domain\Entities;

use Src\InventoryManagement\Domain\ValueObjects\BrandId;
use InvalidArgumentException;

class Brand
{
    private BrandId $id;
    private string $name;
    private ?string $description;
    private ?string $website;
    private ?string $contactEmail;
    private ?string $contactPhone;
    private ?string $logoUrl;
    private bool $isActive;
    private int $productCount;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        BrandId $id,
        string $name,
        ?string $description,
        ?string $website,
        ?string $contactEmail,
        ?string $contactPhone,
        ?string $logoUrl,
        bool $isActive,
        int $productCount,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->validateName($name);
        $this->validateDescription($description);
        $this->validateWebsite($website);
        $this->validateEmail($contactEmail);
        $this->validatePhone($contactPhone);
        $this->validateProductCount($productCount);

        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->website = $website;
        $this->contactEmail = $contactEmail;
        $this->contactPhone = $contactPhone;
        $this->logoUrl = $logoUrl;
        $this->isActive = $isActive;
        $this->productCount = $productCount;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    private function validateName(string $name): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Brand name cannot be empty');
        }

        if (strlen($name) > 100) {
            throw new InvalidArgumentException('Brand name cannot exceed 100 characters');
        }
    }

    private function validateDescription(?string $description): void
    {
        if ($description !== null && strlen($description) > 500) {
            throw new InvalidArgumentException('Brand description cannot exceed 500 characters');
        }
    }

    private function validateWebsite(?string $website): void
    {
        if ($website !== null) {
            if (strlen($website) > 200) {
                throw new InvalidArgumentException('Website URL cannot exceed 200 characters');
            }

            if (!filter_var($website, FILTER_VALIDATE_URL)) {
                throw new InvalidArgumentException('Invalid website URL format');
            }
        }
    }

    private function validateEmail(?string $email): void
    {
        if ($email !== null) {
            if (strlen($email) > 100) {
                throw new InvalidArgumentException('Email cannot exceed 100 characters');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Invalid email format');
            }
        }
    }

    private function validatePhone(?string $phone): void
    {
        if ($phone !== null) {
            if (strlen($phone) > 20) {
                throw new InvalidArgumentException('Phone number cannot exceed 20 characters');
            }

            // Validación básica de formato de teléfono
            if (!preg_match('/^[\d\s\-\+\(\)]+$/', $phone)) {
                throw new InvalidArgumentException('Invalid phone number format');
            }
        }
    }

    private function validateProductCount(int $productCount): void
    {
        if ($productCount < 0) {
            throw new InvalidArgumentException('Product count cannot be negative');
        }
    }

    public function getId(): BrandId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getProductCount(): int
    {
        return $this->productCount;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateName(string $name): void
    {
        $this->validateName($name);
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateDescription(?string $description): void
    {
        $this->validateDescription($description);
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateWebsite(?string $website): void
    {
        $this->validateWebsite($website);
        $this->website = $website;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateContactEmail(?string $email): void
    {
        $this->validateEmail($email);
        $this->contactEmail = $email;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateContactPhone(?string $phone): void
    {
        $this->validatePhone($phone);
        $this->contactPhone = $phone;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateLogoUrl(?string $logoUrl): void
    {
        if ($logoUrl !== null && strlen($logoUrl) > 255) {
            throw new InvalidArgumentException('Logo URL cannot exceed 255 characters');
        }
        $this->logoUrl = $logoUrl;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function incrementProductCount(): void
    {
        $this->productCount++;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function decrementProductCount(): void
    {
        if ($this->productCount > 0) {
            $this->productCount--;
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public static function create(
        string $name,
        ?string $description = null,
        ?string $website = null,
        ?string $contactEmail = null,
        ?string $contactPhone = null,
        ?string $logoUrl = null
    ): self {
        $id = new BrandId(null);

        return new self(
            $id,
            $name,
            $description,
            $website,
            $contactEmail,
            $contactPhone,
            $logoUrl,
            true,
            0,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name,
            'description' => $this->description,
            'website' => $this->website,
            'contact_email' => $this->contactEmail,
            'contact_phone' => $this->contactPhone,
            'logo_url' => $this->logoUrl,
            'is_active' => $this->isActive,
            'product_count' => $this->productCount,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }

    public function hasContactInfo(): bool
    {
        return $this->contactEmail !== null || $this->contactPhone !== null;
    }

    public function getContactInfo(): array
    {
        return [
            'email' => $this->contactEmail,
            'phone' => $this->contactPhone
        ];
    }
}