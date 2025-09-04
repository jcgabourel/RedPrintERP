<?php

namespace Src\CustomerManagement\Domain\ValueObjects;

use InvalidArgumentException;

class PhoneNumber
{
    private string $value;

    public function __construct(string $phoneNumber)
    {
        $this->validate($phoneNumber);
        $this->value = $this->normalize($phoneNumber);
    }

    private function validate(string $phoneNumber): void
    {
        if (empty($phoneNumber)) {
            throw new InvalidArgumentException('Phone number cannot be empty');
        }

        // Remove all non-digit characters for validation
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Basic validation: Mexican phone numbers are typically 10 digits
        // Could be 10 digits (without country code) or 12 digits (with country code)
        if (strlen($cleanNumber) < 10) {
            throw new InvalidArgumentException('Phone number is too short');
        }

        if (strlen($cleanNumber) > 12) {
            throw new InvalidArgumentException('Phone number is too long');
        }
    }

    private function normalize(string $phoneNumber): string
    {
        // Remove all non-digit characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Format as Mexican phone number: +52 (XXX) XXX-XXXX
        if (strlen($cleanNumber) === 10) {
            // Add country code if missing
            $cleanNumber = '52' . $cleanNumber;
        }
        
        return $cleanNumber;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getFormatted(): string
    {
        if (strlen($this->value) === 12) {
            return sprintf(
                '+%s (%s) %s-%s',
                substr($this->value, 0, 2),
                substr($this->value, 2, 3),
                substr($this->value, 5, 3),
                substr($this->value, 8, 4)
            );
        }

        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getFormatted();
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function getCountryCode(): string
    {
        return substr($this->value, 0, 2);
    }

    public function getAreaCode(): string
    {
        return substr($this->value, 2, 3);
    }

    public function getLocalNumber(): string
    {
        return substr($this->value, 5);
    }
}