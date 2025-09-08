<?php

namespace Src\InventoryManagement\Domain\ValueObjects;

use InvalidArgumentException;

class StockQuantity
{
    private int $value;

    public function __construct(int $quantity)
    {
        $this->validate($quantity);
        $this->value = $quantity;
    }

    private function validate(int $quantity): void
    {
        if ($quantity < 0) {
            throw new InvalidArgumentException('Stock quantity cannot be negative');
        }

        if ($quantity > 999999999) {
            throw new InvalidArgumentException('Stock quantity exceeds maximum allowed value');
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function add(self $other): self
    {
        return new self($this->value + $other->getValue());
    }

    public function subtract(self $other): self
    {
        $result = $this->value - $other->getValue();
        if ($result < 0) {
            throw new InvalidArgumentException('Insufficient stock quantity');
        }
        return new self($result);
    }

    public function multiply(int $multiplier): self
    {
        if ($multiplier < 0) {
            throw new InvalidArgumentException('Multiplier cannot be negative');
        }
        
        $result = $this->value * $multiplier;
        if ($result > 999999999) {
            throw new InvalidArgumentException('Result exceeds maximum allowed stock quantity');
        }
        
        return new self($result);
    }

    public function isZero(): bool
    {
        return $this->value === 0;
    }

    public function isPositive(): bool
    {
        return $this->value > 0;
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->value > $other->getValue();
    }

    public function isGreaterThanOrEqual(self $other): bool
    {
        return $this->value >= $other->getValue();
    }

    public function isLessThan(self $other): bool
    {
        return $this->value < $other->getValue();
    }

    public function isLessThanOrEqual(self $other): bool
    {
        return $this->value <= $other->getValue();
    }

    public function isSufficientFor(self $requiredQuantity): bool
    {
        return $this->value >= $requiredQuantity->getValue();
    }

    public function calculatePercentage(self $total): float
    {
        if ($total->isZero()) {
            return 0.0;
        }

        return ($this->value / $total->getValue()) * 100;
    }

    public function format(): string
    {
        return number_format($this->value, 0, '.', ',');
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public static function fromString(string $quantity): self
    {
        if (!is_numeric($quantity)) {
            throw new InvalidArgumentException('Stock quantity must be a numeric value');
        }

        $intValue = (int) $quantity;
        if ((string) $intValue !== $quantity) {
            throw new InvalidArgumentException('Stock quantity must be an integer value');
        }

        return new self($intValue);
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public static function one(): self
    {
        return new self(1);
    }
}