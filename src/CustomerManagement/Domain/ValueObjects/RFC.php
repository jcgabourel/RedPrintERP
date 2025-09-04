<?php

namespace Src\CustomerManagement\Domain\ValueObjects;

use InvalidArgumentException;

class RFC
{
    private string $value;

    public function __construct(string $rfc)
    {
        $this->validate($rfc);
        $this->value = strtoupper(trim($rfc));
    }

    private function validate(string $rfc): void
    {
        if (empty($rfc)) {
            throw new InvalidArgumentException('RFC cannot be empty');
        }

        // RFC validation pattern for Mexican RFC
        // Basic validation: 3-4 letters, 6 digits, 3 alphanumeric (homoclave)
        $pattern = '/^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/';
        
        if (!preg_match($pattern, strtoupper($rfc))) {
            throw new InvalidArgumentException('Invalid RFC format');
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
}