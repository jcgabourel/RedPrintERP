<?php

namespace Src\InventoryManagement\Domain\ValueObjects;

use InvalidArgumentException;

class ProductDescription
{
    private ?string $value;

    public function __construct(?string $description = null)
    {
        if ($description !== null) {
            $this->validate($description);
            $this->value = trim($description);
        } else {
            $this->value = null;
        }
    }

    private function validate(string $description): void
    {
        $description = trim($description);

        if (empty($description)) {
            throw new InvalidArgumentException('Product description cannot be empty if provided');
        }

        if (strlen($description) > 2000) {
            throw new InvalidArgumentException('Product description cannot exceed 2000 characters');
        }

        // Validar caracteres permitidos: letras, números, espacios, puntuación básica
        if (!preg_match('/^[a-zA-Z0-9\s\-\.\,\'\(\)áéíóúÁÉÍÓÚñÑ!?;:"@#$%&*+=]+$/u', $description)) {
            throw new InvalidArgumentException('Product description contains invalid characters');
        }

        // Validar que no tenga múltiples espacios consecutivos
        if (preg_match('/\s{3,}/', $description)) {
            throw new InvalidArgumentException('Product description cannot have multiple consecutive spaces');
        }

        // Validar que no sea solo espacios
        if (trim($description) === '') {
            throw new InvalidArgumentException('Product description cannot be only whitespace');
        }
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === null || $this->value === '';
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function getLength(): int
    {
        return $this->value !== null ? mb_strlen($this->value, 'UTF-8') : 0;
    }

    public function getWordCount(): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        $words = preg_split('/\s+/', $this->value, -1, PREG_SPLIT_NO_EMPTY);
        return count($words);
    }

    public function contains(string $searchTerm): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return stripos($this->value, $searchTerm) !== false;
    }

    public function excerpt(int $maxLength = 150, string $suffix = '...'): string
    {
        if ($this->isEmpty()) {
            return '';
        }

        if (mb_strlen($this->value, 'UTF-8') <= $maxLength) {
            return $this->value;
        }

        $excerpt = mb_substr($this->value, 0, $maxLength - mb_strlen($suffix, 'UTF-8'), 'UTF-8');
        
        // Intentar cortar en un punto de pausa natural
        $lastSpace = mb_strrpos($excerpt, ' ', 0, 'UTF-8');
        $lastPeriod = mb_strrpos($excerpt, '.', 0, 'UTF-8');
        $lastComma = mb_strrpos($excerpt, ',', 0, 'UTF-8');

        $cutPosition = max($lastSpace, $lastPeriod, $lastComma);
        
        if ($cutPosition !== false && $cutPosition > $maxLength / 2) {
            $excerpt = mb_substr($excerpt, 0, $cutPosition + 1, 'UTF-8');
        }

        return trim($excerpt) . $suffix;
    }

    public function toHtml(): string
    {
        if ($this->isEmpty()) {
            return '';
        }

        // Convertir saltos de línea a <br> tags
        $html = nl2br(htmlspecialchars($this->value, ENT_QUOTES, 'UTF-8'));
        
        // Convertir URLs a enlaces
        $html = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
            $html
        );

        return $html;
    }

    public function toPlainText(): string
    {
        if ($this->isEmpty()) {
            return '';
        }

        // Remover tags HTML si existen
        $plainText = strip_tags($this->value);
        
        // Convertir entidades HTML a caracteres normales
        $plainText = html_entity_decode($plainText, ENT_QUOTES, 'UTF-8');
        
        // Normalizar espacios
        $plainText = preg_replace('/\s+/', ' ', $plainText);
        
        return trim($plainText);
    }

    public function countSentences(): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        // Contar oraciones basadas en puntos, signos de exclamación e interrogación
        $sentences = preg_split('/[.!?]+/', $this->value, -1, PREG_SPLIT_NO_EMPTY);
        return count($sentences);
    }

    public function countParagraphs(): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        // Contar párrafos basados en saltos de línea dobles
        $paragraphs = preg_split('/\n\s*\n/', $this->value, -1, PREG_SPLIT_NO_EMPTY);
        return count($paragraphs);
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }

    public static function fromString(?string $description): self
    {
        return new self($description);
    }

    public function equals(self $other): bool
    {
        if ($this->isEmpty() && $other->isEmpty()) {
            return true;
        }

        if ($this->isEmpty() !== $other->isEmpty()) {
            return false;
        }

        return $this->normalize($this->value) === $this->normalize($other->getValue());
    }

    private function normalize(string $text): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', $text)), 'UTF-8');
    }

    public function isSimilarTo(self $other, float $similarityThreshold = 70.0): bool
    {
        if ($this->isEmpty() && $other->isEmpty()) {
            return true;
        }

        if ($this->isEmpty() !== $other->isEmpty()) {
            return false;
        }

        $similarity = $this->calculateSimilarity($other->getValue());
        return $similarity >= $similarityThreshold;
    }

    private function calculateSimilarity(string $otherDescription): float
    {
        $thisNormalized = $this->normalize($this->value);
        $otherNormalized = $this->normalize($otherDescription);

        similar_text($thisNormalized, $otherNormalized, $similarity);
        return $similarity;
    }
}