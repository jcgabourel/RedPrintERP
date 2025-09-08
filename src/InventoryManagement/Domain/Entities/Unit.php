<?php

namespace Src\InventoryManagement\Domain\Entities;

use Src\InventoryManagement\Domain\ValueObjects\UnitId;
use InvalidArgumentException;

class Unit
{
    private UnitId $id;
    private string $name;
    private string $abbreviation;
    private string $type;
    private float $conversionFactor;
    private bool $isBaseUnit;
    private bool $isActive;
    private int $usageCount;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        UnitId $id,
        string $name,
        string $abbreviation,
        string $type,
        float $conversionFactor,
        bool $isBaseUnit,
        bool $isActive,
        int $usageCount,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->validateName($name);
        $this->validateAbbreviation($abbreviation);
        $this->validateType($type);
        $this->validateConversionFactor($conversionFactor);
        $this->validateUsageCount($usageCount);

        $this->id = $id;
        $this->name = $name;
        $this->abbreviation = $abbreviation;
        $this->type = $type;
        $this->conversionFactor = $conversionFactor;
        $this->isBaseUnit = $isBaseUnit;
        $this->isActive = $isActive;
        $this->usageCount = $usageCount;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    private function validateName(string $name): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Unit name cannot be empty');
        }

        if (strlen($name) > 50) {
            throw new InvalidArgumentException('Unit name cannot exceed 50 characters');
        }
    }

    private function validateAbbreviation(string $abbreviation): void
    {
        if (empty(trim($abbreviation))) {
            throw new InvalidArgumentException('Unit abbreviation cannot be empty');
        }

        if (strlen($abbreviation) > 10) {
            throw new InvalidArgumentException('Unit abbreviation cannot exceed 10 characters');
        }

        if (!preg_match('/^[a-zA-Z]+$/', $abbreviation)) {
            throw new InvalidArgumentException('Unit abbreviation can only contain letters');
        }
    }

    private function validateType(string $type): void
    {
        $allowedTypes = ['weight', 'volume', 'length', 'area', 'count', 'time', 'temperature', 'other'];
        
        if (!in_array($type, $allowedTypes)) {
            throw new InvalidArgumentException(
                sprintf('Invalid unit type. Allowed types are: %s', implode(', ', $allowedTypes))
            );
        }
    }

    private function validateConversionFactor(float $conversionFactor): void
    {
        if ($conversionFactor <= 0) {
            throw new InvalidArgumentException('Conversion factor must be greater than zero');
        }

        if ($conversionFactor > 1000000) {
            throw new InvalidArgumentException('Conversion factor cannot exceed 1,000,000');
        }
    }

    private function validateUsageCount(int $usageCount): void
    {
        if ($usageCount < 0) {
            throw new InvalidArgumentException('Usage count cannot be negative');
        }
    }

    public function getId(): UnitId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAbbreviation(): string
    {
        return $this->abbreviation;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getConversionFactor(): float
    {
        return $this->conversionFactor;
    }

    public function isBaseUnit(): bool
    {
        return $this->isBaseUnit;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getUsageCount(): int
    {
        return $this->usageCount;
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

    public function updateAbbreviation(string $abbreviation): void
    {
        $this->validateAbbreviation($abbreviation);
        $this->abbreviation = $abbreviation;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateConversionFactor(float $conversionFactor): void
    {
        $this->validateConversionFactor($conversionFactor);
        $this->conversionFactor = $conversionFactor;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function markAsBaseUnit(): void
    {
        $this->isBaseUnit = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function unmarkAsBaseUnit(): void
    {
        $this->isBaseUnit = false;
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

    public function incrementUsageCount(): void
    {
        $this->usageCount++;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function decrementUsageCount(): void
    {
        if ($this->usageCount > 0) {
            $this->usageCount--;
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function convertToBaseUnit(float $value): float
    {
        return $value * $this->conversionFactor;
    }

    public function convertFromBaseUnit(float $value): float
    {
        return $value / $this->conversionFactor;
    }

    public function convertTo(Unit $targetUnit, float $value): float
    {
        if ($this->type !== $targetUnit->getType()) {
            throw new InvalidArgumentException('Cannot convert between different unit types');
        }

        // Convertir a unidad base primero
        $baseValue = $this->convertToBaseUnit($value);
        
        // Convertir desde unidad base a la unidad objetivo
        return $targetUnit->convertFromBaseUnit($baseValue);
    }

    public static function create(
        string $name,
        string $abbreviation,
        string $type,
        float $conversionFactor = 1.0,
        bool $isBaseUnit = false
    ): self {
        $id = new UnitId(null);

        return new self(
            $id,
            $name,
            $abbreviation,
            $type,
            $conversionFactor,
            $isBaseUnit,
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
            'abbreviation' => $this->abbreviation,
            'type' => $this->type,
            'conversion_factor' => $this->conversionFactor,
            'is_base_unit' => $this->isBaseUnit,
            'is_active' => $this->isActive,
            'usage_count' => $this->usageCount,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }

    public function getDisplayName(): string
    {
        return "{$this->name} ({$this->abbreviation})";
    }

    public function isOfType(string $type): bool
    {
        return $this->type === $type;
    }

    public function canConvertTo(Unit $otherUnit): bool
    {
        return $this->type === $otherUnit->getType();
    }
}