<?php

namespace Src\InventoryManagement\Domain\Entities;

use Src\InventoryManagement\Domain\ValueObjects\WarehouseId;
use Src\InventoryManagement\Domain\ValueObjects\Dimensions;
use Src\InventoryManagement\Domain\ValueObjects\StockQuantity;
use InvalidArgumentException;

class Warehouse
{
    private WarehouseId $id;
    private string $name;
    private string $code;
    private string $address;
    private ?string $city;
    private ?string $state;
    private ?string $country;
    private ?string $postalCode;
    private ?float $latitude;
    private ?float $longitude;
    private ?string $contactPerson;
    private ?string $contactPhone;
    private ?string $contactEmail;
    private Dimensions $dimensions;
    private StockQuantity $capacity;
    private StockQuantity $currentStock;
    private bool $isActive;
    private bool $isDefault;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        WarehouseId $id,
        string $name,
        string $code,
        string $address,
        ?string $city,
        ?string $state,
        ?string $country,
        ?string $postalCode,
        ?float $latitude,
        ?float $longitude,
        ?string $contactPerson,
        ?string $contactPhone,
        ?string $contactEmail,
        Dimensions $dimensions,
        StockQuantity $capacity,
        StockQuantity $currentStock,
        bool $isActive,
        bool $isDefault,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->validateName($name);
        $this->validateCode($code);
        $this->validateAddress($address);
        $this->validateContactInfo($contactEmail, $contactPhone);
        $this->validateCoordinates($latitude, $longitude);

        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->postalCode = $postalCode;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->contactPerson = $contactPerson;
        $this->contactPhone = $contactPhone;
        $this->contactEmail = $contactEmail;
        $this->dimensions = $dimensions;
        $this->capacity = $capacity;
        $this->currentStock = $currentStock;
        $this->isActive = $isActive;
        $this->isDefault = $isDefault;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    private function validateName(string $name): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Warehouse name cannot be empty');
        }

        if (strlen($name) > 100) {
            throw new InvalidArgumentException('Warehouse name cannot exceed 100 characters');
        }
    }

    private function validateCode(string $code): void
    {
        if (empty(trim($code))) {
            throw new InvalidArgumentException('Warehouse code cannot be empty');
        }

        if (strlen($code) > 20) {
            throw new InvalidArgumentException('Warehouse code cannot exceed 20 characters');
        }

        if (!preg_match('/^[A-Z0-9_\-]+$/', $code)) {
            throw new InvalidArgumentException('Warehouse code can only contain uppercase letters, numbers, hyphens, and underscores');
        }
    }

    private function validateAddress(string $address): void
    {
        if (empty(trim($address))) {
            throw new InvalidArgumentException('Warehouse address cannot be empty');
        }

        if (strlen($address) > 255) {
            throw new InvalidArgumentException('Warehouse address cannot exceed 255 characters');
        }
    }

    private function validateContactInfo(?string $email, ?string $phone): void
    {
        if ($email !== null) {
            if (strlen($email) > 100) {
                throw new InvalidArgumentException('Contact email cannot exceed 100 characters');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Invalid contact email format');
            }
        }

        if ($phone !== null) {
            if (strlen($phone) > 20) {
                throw new InvalidArgumentException('Contact phone cannot exceed 20 characters');
            }
        }
    }

    private function validateCoordinates(?float $latitude, ?float $longitude): void
    {
        if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
            throw new InvalidArgumentException('Latitude must be between -90 and 90');
        }

        if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
            throw new InvalidArgumentException('Longitude must be between -180 and 180');
        }

        // Si se proporciona una coordenada, la otra también debe proporcionarse
        if (($latitude !== null && $longitude === null) || ($latitude === null && $longitude !== null)) {
            throw new InvalidArgumentException('Both latitude and longitude must be provided together');
        }
    }

    public function getId(): WarehouseId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function getDimensions(): Dimensions
    {
        return $this->dimensions;
    }

    public function getCapacity(): StockQuantity
    {
        return $this->capacity;
    }

    public function getCurrentStock(): StockQuantity
    {
        return $this->currentStock;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
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

    public function updateCode(string $code): void
    {
        $this->validateCode($code);
        $this->code = $code;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateAddress(string $address): void
    {
        $this->validateAddress($address);
        $this->address = $address;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateLocation(?string $city, ?string $state, ?string $country, ?string $postalCode): void
    {
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->postalCode = $postalCode;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateCoordinates(?float $latitude, ?float $longitude): void
    {
        $this->validateCoordinates($latitude, $longitude);
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateContactInfo(?string $contactPerson, ?string $contactPhone, ?string $contactEmail): void
    {
        $this->validateContactInfo($contactEmail, $contactPhone);
        $this->contactPerson = $contactPerson;
        $this->contactPhone = $contactPhone;
        $this->contactEmail = $contactEmail;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateDimensions(Dimensions $dimensions): void
    {
        $this->dimensions = $dimensions;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateCapacity(StockQuantity $capacity): void
    {
        $this->capacity = $capacity;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateCurrentStock(StockQuantity $currentStock): void
    {
        $this->currentStock = $currentStock;
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

    public function markAsDefault(): void
    {
        $this->isDefault = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function unmarkAsDefault(): void
    {
        $this->isDefault = false;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function addStock(StockQuantity $quantity): void
    {
        $newStock = $this->currentStock->add($quantity);
        
        if ($newStock->isGreaterThan($this->capacity)) {
            throw new InvalidArgumentException('Cannot add stock: exceeds warehouse capacity');
        }

        $this->currentStock = $newStock;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function removeStock(StockQuantity $quantity): void
    {
        $this->currentStock = $this->currentStock->subtract($quantity);
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getAvailableCapacity(): StockQuantity
    {
        return $this->capacity->subtract($this->currentStock);
    }

    public function getUtilizationPercentage(): float
    {
        if ($this->capacity->isZero()) {
            return 0.0;
        }

        return ($this->currentStock->getValue() / $this->capacity->getValue()) * 100;
    }

    public function hasAvailableCapacity(StockQuantity $requiredQuantity): bool
    {
        $availableCapacity = $this->getAvailableCapacity();
        return $availableCapacity->isGreaterThanOrEqual($requiredQuantity);
    }

    public static function create(
        string $name,
        string $code,
        string $address,
        ?string $city = null,
        ?string $state = null,
        ?string $country = null,
        ?string $postalCode = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $contactPerson = null,
        ?string $contactPhone = null,
        ?string $contactEmail = null,
        Dimensions $dimensions,
        StockQuantity $capacity,
        bool $isDefault = false
    ): self {
        $id = new WarehouseId(null);
        $currentStock = new StockQuantity(0);

        return new self(
            $id,
            $name,
            $code,
            $address,
            $city,
            $state,
            $country,
            $postalCode,
            $latitude,
            $longitude,
            $contactPerson,
            $contactPhone,
            $contactEmail,
            $dimensions,
            $capacity,
            $currentStock,
            true,
            $isDefault,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postalCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'contact_person' => $this->contactPerson,
            'contact_phone' => $this->contactPhone,
            'contact_email' => $this->contactEmail,
            'dimensions' => $this->dimensions->toArray(),
            'capacity' => $this->capacity->getValue(),
            'current_stock' => $this->currentStock->getValue(),
            'available_capacity' => $this->getAvailableCapacity()->getValue(),
            'utilization_percentage' => $this->getUtilizationPercentage(),
            'is_active' => $this->isActive,
            'is_default' => $this->isDefault,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }

    public function getFullAddress(): string
    {
        $parts = [
            $this->address,
            $this->city,
            $this->state,
            $this->postalCode,
            $this->country
        ];

        return implode(', ', array_filter($parts));
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function hasContactInfo(): bool
    {
        return $this->contactPerson !== null || $this->contactPhone !== null || $this->contactEmail !== null;
    }
}