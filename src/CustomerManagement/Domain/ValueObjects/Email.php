<?php

namespace Src\CustomerManagement\Domain\ValueObjects;

use InvalidArgumentException;

class Email
{
    private string $value;

    public function __construct(string $email)
    {
        $this->validate($email);
        $this->value = strtolower(trim($email));
    }

    private function validate(string $email): void
    {
        if (empty($email)) {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
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

    public function getDomain(): string
    {
        $parts = explode('@', $this->value);
        return $parts[1] ?? '';
    }

    public function getLocalPart(): string
    {
        $parts = explode('@', $this->value);
        return $parts[0] ?? '';
    }
}