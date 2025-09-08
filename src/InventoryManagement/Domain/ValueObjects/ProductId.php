<?php

namespace Src\InventoryManagement\Domain\ValueObjects;

use InvalidArgumentException;

class ProductId
{
    private ?int $value;

    public function __construct(?int $id = null)
    {
        if ($id !== null && $id <= 0) {
            throw new InvalidArgumentException('Product ID must be a positive integer or null');
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
        return (string) $this->value;
    }

    public static function fromString(string $id): self
    {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException('Product ID must be numeric');
        }

        $id = (int) $id;
        if ($id <= 0) {
            throw new InvalidArgumentException('Product ID must be a positive integer');
        }

        return new self($id);
    }
}