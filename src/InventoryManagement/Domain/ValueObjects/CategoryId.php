<?php

namespace Src\InventoryManagement\Domain\ValueObjects;

use InvalidArgumentException;

class CategoryId
{
    private ?int $value;

    public function __construct(?int $id = null)
    {
        if ($id !== null && $id <= 0) {
            throw new InvalidArgumentException('Category ID must be a positive integer or null');
        }
        $this->value = $id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function isNull(): bool
    {
        return $this->value === null;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value !== null ? (string) $this->value : 'null';
    }

    public static function fromString(?string $id): self
    {
        if ($id === null || $id === 'null') {
            return new self(null);
        }

        if (!is_numeric($id) || (int) $id <= 0) {
            throw new InvalidArgumentException('Category ID must be a positive integer or null');
        }

        return new self((int) $id);
    }
}