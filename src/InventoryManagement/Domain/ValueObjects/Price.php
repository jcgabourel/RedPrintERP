<?php

namespace Src\InventoryManagement\Domain\ValueObjects;

use InvalidArgumentException;

class Price
{
    private float $value;

    public function __construct(float $price)
    {
        $this->validate($price);
        $this->value = round($price, 2);
    }

    private function validate(float $price): void
    {
        if ($price < 0) {
            throw new InvalidArgumentException('Price cannot be negative');
        }

        if ($price > 999999999.99) {
            throw new InvalidArgumentException('Price exceeds maximum allowed value');
        }
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return abs($this->value - $other->getValue()) < 0.001;
    }

    public function add(self $other): self
    {
        return new self($this->value + $other->getValue());
    }

    public function subtract(self $other): self
    {
        $result = $this->value - $other->getValue();
        if ($result < 0) {
            throw new InvalidArgumentException('Resulting price cannot be negative');
        }
        return new self($result);
    }

    public function multiply(float $multiplier): self
    {
        if ($multiplier < 0) {
            throw new InvalidArgumentException('Multiplier cannot be negative');
        }
        return new self($this->value * $multiplier);
    }

    public function divide(float $divisor): self
    {
        if ($divisor <= 0) {
            throw new InvalidArgumentException('Divisor must be greater than zero');
        }
        return new self($this->value / $divisor);
    }

    public function applyDiscount(float $discountPercentage): self
    {
        if ($discountPercentage < 0 || $discountPercentage > 100) {
            throw new InvalidArgumentException('Discount percentage must be between 0 and 100');
        }
        
        $discountAmount = $this->value * ($discountPercentage / 100);
        return new self($this->value - $discountAmount);
    }

    public function applyTax(float $taxPercentage): self
    {
        if ($taxPercentage < 0) {
            throw new InvalidArgumentException('Tax percentage cannot be negative');
        }
        
        $taxAmount = $this->value * ($taxPercentage / 100);
        return new self($this->value + $taxAmount);
    }

    public function isZero(): bool
    {
        return $this->value == 0;
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->value > $other->getValue();
    }

    public function isLessThan(self $other): bool
    {
        return $this->value < $other->getValue();
    }

    public function format(string $currency = 'MXN', string $locale = 'es_MX'): string
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($this->value, $currency);
    }

    public function __toString(): string
    {
        return number_format($this->value, 2, '.', '');
    }

    public static function fromString(string $price): self
    {
        if (!is_numeric($price)) {
            throw new InvalidArgumentException('Price must be a numeric value');
        }

        return new self((float) $price);
    }
}