<?php

namespace Src\InventoryManagement\Domain\ValueObjects;

use InvalidArgumentException;

class Weight
{
    private float $value;
    private string $unit;

    public function __construct(float $weight, string $unit = 'kg')
    {
        $this->validateWeight($weight);
        $this->validateUnit($unit);

        $this->value = round($weight, 2);
        $this->unit = strtolower($unit);
    }

    private function validateWeight(float $weight): void
    {
        if ($weight <= 0) {
            throw new InvalidArgumentException('Weight must be greater than zero');
        }

        if ($weight > 10000) {
            throw new InvalidArgumentException('Weight exceeds maximum allowed value (10000)');
        }
    }

    private function validateUnit(string $unit): void
    {
        $allowedUnits = ['mg', 'g', 'kg', 'oz', 'lb'];
        
        if (!in_array(strtolower($unit), $allowedUnits)) {
            throw new InvalidArgumentException(
                sprintf('Invalid unit. Allowed units are: %s', implode(', ', $allowedUnits))
            );
        }
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function convertTo(string $targetUnit): self
    {
        if ($this->unit === $targetUnit) {
            return $this;
        }

        $conversionFactors = $this->getConversionFactors();
        
        if (!isset($conversionFactors[$this->unit][$targetUnit])) {
            throw new InvalidArgumentException("Conversion from {$this->unit} to {$targetUnit} is not supported");
        }

        $factor = $conversionFactors[$this->unit][$targetUnit];
        $newWeight = $this->value * $factor;

        return new self($newWeight, $targetUnit);
    }

    private function getConversionFactors(): array
    {
        return [
            'mg' => [
                'g' => 0.001,
                'kg' => 0.000001,
                'oz' => 0.000035274,
                'lb' => 0.00000220462
            ],
            'g' => [
                'mg' => 1000,
                'kg' => 0.001,
                'oz' => 0.035274,
                'lb' => 0.00220462
            ],
            'kg' => [
                'mg' => 1000000,
                'g' => 1000,
                'oz' => 35.274,
                'lb' => 2.20462
            ],
            'oz' => [
                'mg' => 28349.5,
                'g' => 28.3495,
                'kg' => 0.0283495,
                'lb' => 0.0625
            ],
            'lb' => [
                'mg' => 453592,
                'g' => 453.592,
                'kg' => 0.453592,
                'oz' => 16
            ]
        ];
    }

    public function equals(self $other): bool
    {
        $otherInSameUnit = $other->convertTo($this->unit);
        return abs($this->value - $otherInSameUnit->value) < 0.001;
    }

    public function add(self $other): self
    {
        $otherInSameUnit = $other->convertTo($this->unit);
        $newWeight = $this->value + $otherInSameUnit->value;

        return new self($newWeight, $this->unit);
    }

    public function subtract(self $other): self
    {
        $otherInSameUnit = $other->convertTo($this->unit);
        $newWeight = $this->value - $otherInSameUnit->value;

        if ($newWeight <= 0) {
            throw new InvalidArgumentException('Resulting weight cannot be zero or negative');
        }

        return new self($newWeight, $this->unit);
    }

    public function multiply(float $multiplier): self
    {
        if ($multiplier <= 0) {
            throw new InvalidArgumentException('Multiplier must be greater than zero');
        }

        $newWeight = $this->value * $multiplier;

        return new self($newWeight, $this->unit);
    }

    public function divide(float $divisor): self
    {
        if ($divisor <= 0) {
            throw new InvalidArgumentException('Divisor must be greater than zero');
        }

        $newWeight = $this->value / $divisor;

        return new self($newWeight, $this->unit);
    }

    public function isHeavierThan(self $other): bool
    {
        $otherInSameUnit = $other->convertTo($this->unit);
        return $this->value > $otherInSameUnit->value;
    }

    public function isLighterThan(self $other): bool
    {
        $otherInSameUnit = $other->convertTo($this->unit);
        return $this->value < $otherInSameUnit->value;
    }

    public function isEqualTo(self $other): bool
    {
        return $this->equals($other);
    }

    public function getFormattedWeight(): string
    {
        return number_format($this->value, 2) . ' ' . $this->unit;
    }

    public function calculateDensity(float $volume, string $volumeUnit = 'cm³'): float
    {
        if ($volume <= 0) {
            throw new InvalidArgumentException('Volume must be greater than zero');
        }

        // Convertir a unidades consistentes para cálculo de densidad
        $weightInGrams = $this->convertTo('g')->value;
        
        // Convertir volumen a cm³ si es necesario
        $volumeInCm3 = $this->convertVolumeToCm3($volume, $volumeUnit);

        return round($weightInGrams / $volumeInCm3, 4);
    }

    private function convertVolumeToCm3(float $volume, string $volumeUnit): float
    {
        $conversionFactors = [
            'mm³' => 0.001,
            'cm³' => 1,
            'm³' => 1000000,
            'in³' => 16.3871,
            'ft³' => 28316.8
        ];

        $volumeUnit = strtolower($volumeUnit);
        
        if (!isset($conversionFactors[$volumeUnit])) {
            throw new InvalidArgumentException("Unsupported volume unit: {$volumeUnit}");
        }

        return $volume * $conversionFactors[$volumeUnit];
    }

    public function calculateShippingCost(float $costPerKg = 50.0): Price
    {
        $weightInKg = $this->convertTo('kg')->value;
        $shippingCost = $weightInKg * $costPerKg;

        return new Price($shippingCost);
    }

    public function __toString(): string
    {
        return $this->getFormattedWeight();
    }

    public static function fromArray(array $weightData): self
    {
        if (!isset($weightData['value'])) {
            throw new InvalidArgumentException('Weight data must contain value');
        }

        $unit = $weightData['unit'] ?? 'kg';

        return new self((float) $weightData['value'], $unit);
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'unit' => $this->unit,
            'formatted' => $this->getFormattedWeight()
        ];
    }

    public static function zero(string $unit = 'kg'): self
    {
        return new self(0.01, $unit); // Valor mínimo en lugar de cero
    }

    public static function fromString(string $weightString): self
    {
        if (!preg_match('/^([\d\.]+)\s*([a-zA-Z]+)$/', trim($weightString), $matches)) {
            throw new InvalidArgumentException('Invalid weight string format. Expected format: "value unit"');
        }

        $value = (float) $matches[1];
        $unit = strtolower($matches[2]);

        return new self($value, $unit);
    }

    public function isWithinRange(self $min, self $max): bool
    {
        $minInSameUnit = $min->convertTo($this->unit);
        $maxInSameUnit = $max->convertTo($this->unit);

        return $this->value >= $minInSameUnit->value && $this->value <= $maxInSameUnit->value;
    }
}