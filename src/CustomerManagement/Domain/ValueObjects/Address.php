<?php

namespace Src\CustomerManagement\Domain\ValueObjects;

use InvalidArgumentException;

class Address
{
    private string $value;

    public function __construct(string $address)
    {
        $this->validate($address);
        $this->value = trim($address);
    }

    private function validate(string $address): void
    {
        if (empty($address)) {
            throw new InvalidArgumentException('Address cannot be empty');
        }

        if (strlen($address) < 10) {
            throw new InvalidArgumentException('Address is too short');
        }

        if (strlen($address) > 500) {
            throw new InvalidArgumentException('Address is too long');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function getLines(): array
    {
        return array_filter(
            array_map('trim', explode("\n", $this->value)),
            function ($line) {
                return !empty($line);
            }
        );
    }

    public function getStreet(): ?string
    {
        $lines = $this->getLines();
        return $lines[0] ?? null;
    }

    public function getNeighborhood(): ?string
    {
        $lines = $this->getLines();
        return $lines[1] ?? null;
    }

    public function getCityState(): ?string
    {
        $lines = $this->getLines();
        return $lines[2] ?? null;
    }

    public function getZipCode(): ?string
    {
        // Try to extract zip code from address
        if (preg_match('/\b\d{5}\b/', $this->value, $matches)) {
            return $matches[0];
        }

        return null;
    }

    public function contains(string $searchTerm): bool
    {
        return stripos($this->value, $searchTerm) !== false;
    }
}