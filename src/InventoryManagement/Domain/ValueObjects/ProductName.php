<?php

namespace Src\InventoryManagement\Domain\ValueObjects;

use InvalidArgumentException;

class ProductName
{
    private string $value;

    public function __construct(string $name)
    {
        $this->validate($name);
        $this->value = trim($name);
    }

    private function validate(string $name): void
    {
        $name = trim($name);

        if (empty($name)) {
            throw new InvalidArgumentException('Product name cannot be empty');
        }

        if (strlen($name) < 2) {
            throw new InvalidArgumentException('Product name must be at least 2 characters long');
        }

        if (strlen($name) > 255) {
            throw new InvalidArgumentException('Product name cannot exceed 255 characters');
        }

        // Validar que no contenga solo números o caracteres especiales
        if (!preg_match('/[a-zA-Z]/', $name)) {
            throw new InvalidArgumentException('Product name must contain at least one letter');
        }

        // Validar caracteres permitidos: letras, números, espacios, guiones, apóstrofes, paréntesis
        if (!preg_match('/^[a-zA-Z0-9\s\-\.\,\'\(\)áéíóúÁÉÍÓÚñÑ]+$/u', $name)) {
            throw new InvalidArgumentException('Product name contains invalid characters');
        }

        // Validar que no empiece o termine con caracteres especiales
        if (preg_match('/^[\s\-\.\,\'\(\)]|[\s\-\.\,\'\(\)]$/', $name)) {
            throw new InvalidArgumentException('Product name cannot start or end with special characters');
        }

        // Validar que no tenga espacios múltiples consecutivos
        if (preg_match('/\s{2,}/', $name)) {
            throw new InvalidArgumentException('Product name cannot have multiple consecutive spaces');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->normalize($this->value) === $this->normalize($other->getValue());
    }

    private function normalize(string $name): string
    {
        return mb_strtolower(trim($name), 'UTF-8');
    }

    public function contains(string $searchTerm): bool
    {
        return stripos($this->value, $searchTerm) !== false;
    }

    public function startsWith(string $prefix): bool
    {
        return stripos($this->value, $prefix) === 0;
    }

    public function endsWith(string $suffix): bool
    {
        $suffixLength = strlen($suffix);
        if ($suffixLength === 0) {
            return true;
        }

        return stripos($this->value, $suffix, strlen($this->value) - $suffixLength) !== false;
    }

    public function getWordCount(): int
    {
        $words = preg_split('/\s+/', $this->value, -1, PREG_SPLIT_NO_EMPTY);
        return count($words);
    }

    public function getLength(): int
    {
        return mb_strlen($this->value, 'UTF-8');
    }

    public function toUpperCase(): string
    {
        return mb_strtoupper($this->value, 'UTF-8');
    }

    public function toLowerCase(): string
    {
        return mb_strtolower($this->value, 'UTF-8');
    }

    public function toTitleCase(): string
    {
        return mb_convert_case($this->value, MB_CASE_TITLE, 'UTF-8');
    }

    public function abbreviate(int $maxLength = 50, string $suffix = '...'): string
    {
        if (mb_strlen($this->value, 'UTF-8') <= $maxLength) {
            return $this->value;
        }

        $abbreviated = mb_substr($this->value, 0, $maxLength - mb_strlen($suffix, 'UTF-8'), 'UTF-8');
        
        // Asegurar que no corte en medio de una palabra si es posible
        $lastSpace = mb_strrpos($abbreviated, ' ', 0, 'UTF-8');
        if ($lastSpace !== false && $lastSpace > $maxLength / 2) {
            $abbreviated = mb_substr($abbreviated, 0, $lastSpace, 'UTF-8');
        }

        return $abbreviated . $suffix;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function isSimilarTo(self $other, float $similarityThreshold = 80.0): bool
    {
        $similarity = $this->calculateSimilarity($other->getValue());
        return $similarity >= $similarityThreshold;
    }

    private function calculateSimilarity(string $otherName): float
    {
        $thisNormalized = $this->normalize($this->value);
        $otherNormalized = $this->normalize($otherName);

        similar_text($thisNormalized, $otherNormalized, $similarity);
        return $similarity;
    }
}