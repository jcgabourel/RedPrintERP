<?php

namespace Src\InventoryManagement\Domain\ValueObjects;

use Illuminate\Support\Str;

class ProductSlug
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Product slug cannot be empty');
        }

        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            throw new \InvalidArgumentException('Invalid product slug format');
        }

        $this->value = $value;
    }

    public static function fromName(string $name): self
    {
        $slug = Str::slug($name);
        
        if (empty($slug)) {
            throw new \InvalidArgumentException('Cannot generate slug from empty name');
        }

        return new self($slug);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ProductSlug $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}