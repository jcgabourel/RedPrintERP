<?php

namespace Src\InventoryManagement\Domain\ValueObjects;

use InvalidArgumentException;

class Sku
{
    private string $value;

    public function __construct(string $sku)
    {
        $this->validate($sku);
        $this->value = strtoupper(trim($sku));
    }

    private function validate(string $sku): void
    {
        if (empty($sku)) {
            throw new InvalidArgumentException('SKU cannot be empty');
        }

        if (strlen($sku) > 50) {
            throw new InvalidArgumentException('SKU cannot exceed 50 characters');
        }

        // Validar formato básico: alfanumérico con guiones o underscores
        if (!preg_match('/^[A-Z0-9-_]+$/i', $sku)) {
            throw new InvalidArgumentException('SKU can only contain letters, numbers, hyphens, and underscores');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getPrefix(): string
    {
        $parts = explode('-', $this->value);
        return $parts[0] ?? '';
    }

    public function getSuffix(): string
    {
        $parts = explode('-', $this->value);
        return $parts[count($parts) - 1] ?? '';
    }

    public function isSimilarTo(self $other, int $similarityThreshold = 3): bool
    {
        $similarity = similar_text($this->value, $other->getValue(), $percent);
        return $percent >= $similarityThreshold;
    }
}