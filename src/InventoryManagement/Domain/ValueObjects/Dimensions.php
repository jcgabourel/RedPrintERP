<?php

namespace Src\InventoryManagement\Domain\ValueObjects;

use InvalidArgumentException;

class Dimensions
{
    private float $length;
    private float $width;
    private float $height;
    private string $unit;

    public function __construct(float $length, float $width, float $height, ?string $unit = null)
    {
        $this->validateDimensions($length, $width, $height);
        
        // Si no se proporciona unidad, usar 'cm' por defecto
        $unit = $unit ?? 'cm';
        $this->validateUnit($unit);

        $this->length = round($length, 2);
        $this->width = round($width, 2);
        $this->height = round($height, 2);
        $this->unit = strtolower($unit);
    }

    private function validateDimensions(float $length, float $width, float $height): void
    {
        if ($length <= 0) {
            throw new InvalidArgumentException('Length must be greater than zero');
        }

        if ($width <= 0) {
            throw new InvalidArgumentException('Width must be greater than zero');
        }

        if ($height <= 0) {
            throw new InvalidArgumentException('Height must be greater than zero');
        }

        if ($length > 1000) {
            throw new InvalidArgumentException('Length exceeds maximum allowed value (1000)');
        }

        if ($width > 1000) {
            throw new InvalidArgumentException('Width exceeds maximum allowed value (1000)');
        }

        if ($height > 1000) {
            throw new InvalidArgumentException('Height exceeds maximum allowed value (1000)');
        }
    }

    private function validateUnit(string $unit): void
    {
        $allowedUnits = ['mm', 'cm', 'm', 'in', 'ft'];
        
        if (!in_array(strtolower($unit), $allowedUnits)) {
            throw new InvalidArgumentException(
                sprintf('Invalid unit. Allowed units are: %s', implode(', ', $allowedUnits))
            );
        }
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getVolume(): float
    {
        return round($this->length * $this->width * $this->height, 2);
    }

    public function getVolumeWithUnit(): string
    {
        $volume = $this->getVolume();
        $volumeUnit = $this->getVolumeUnit();

        return number_format($volume, 2) . ' ' . $volumeUnit;
    }

    private function getVolumeUnit(): string
    {
        $units = [
            'mm' => 'mm³',
            'cm' => 'cm³',
            'm' => 'm³',
            'in' => 'in³',
            'ft' => 'ft³'
        ];

        return $units[$this->unit] ?? $this->unit . '³';
    }

    public function getSurfaceArea(): float
    {
        $area1 = 2 * ($this->length * $this->width);
        $area2 = 2 * ($this->length * $this->height);
        $area3 = 2 * ($this->width * $this->height);
        
        return round($area1 + $area2 + $area3, 2);
    }

    public function getSurfaceAreaWithUnit(): string
    {
        $area = $this->getSurfaceArea();
        $areaUnit = $this->getAreaUnit();

        return number_format($area, 2) . ' ' . $areaUnit;
    }

    private function getAreaUnit(): string
    {
        $units = [
            'mm' => 'mm²',
            'cm' => 'cm²',
            'm' => 'm²',
            'in' => 'in²',
            'ft' => 'ft²'
        ];

        return $units[$this->unit] ?? $this->unit . '²';
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
        
        $newLength = $this->length * $factor;
        $newWidth = $this->width * $factor;
        $newHeight = $this->height * $factor;

        return new self($newLength, $newWidth, $newHeight, $targetUnit);
    }

    private function getConversionFactors(): array
    {
        return [
            'mm' => [
                'cm' => 0.1,
                'm' => 0.001,
                'in' => 0.0393701,
                'ft' => 0.00328084
            ],
            'cm' => [
                'mm' => 10,
                'm' => 0.01,
                'in' => 0.393701,
                'ft' => 0.0328084
            ],
            'm' => [
                'mm' => 1000,
                'cm' => 100,
                'in' => 39.3701,
                'ft' => 3.28084
            ],
            'in' => [
                'mm' => 25.4,
                'cm' => 2.54,
                'm' => 0.0254,
                'ft' => 0.0833333
            ],
            'ft' => [
                'mm' => 304.8,
                'cm' => 30.48,
                'm' => 0.3048,
                'in' => 12
            ]
        ];
    }

    public function equals(self $other): bool
    {
        // Convertir ambas dimensiones a la misma unidad para comparar
        $otherInSameUnit = $other->convertTo($this->unit);

        return abs($this->length - $otherInSameUnit->length) < 0.001 &&
               abs($this->width - $otherInSameUnit->width) < 0.001 &&
               abs($this->height - $otherInSameUnit->height) < 0.001;
    }

    public function isLargerThan(self $other): bool
    {
        $otherVolume = $other->convertTo($this->unit)->getVolume();
        return $this->getVolume() > $otherVolume;
    }

    public function isSmallerThan(self $other): bool
    {
        $otherVolume = $other->convertTo($this->unit)->getVolume();
        return $this->getVolume() < $otherVolume;
    }

    public function canFitInside(self $container): bool
    {
        $thisInContainerUnit = $this->convertTo($container->unit);

        return $thisInContainerUnit->length <= $container->length &&
               $thisInContainerUnit->width <= $container->width &&
               $thisInContainerUnit->height <= $container->height;
    }

    public function getFormattedDimensions(): string
    {
        return sprintf(
            '%s × %s × %s %s',
            number_format($this->length, 2),
            number_format($this->width, 2),
            number_format($this->height, 2),
            $this->unit
        );
    }

    public function __toString(): string
    {
        return $this->getFormattedDimensions();
    }

    public static function fromArray(array $dimensions): self
    {
        if (!isset($dimensions['length'], $dimensions['width'], $dimensions['height'])) {
            throw new InvalidArgumentException('Dimensions array must contain length, width, and height');
        }

        $unit = $dimensions['unit'] ?? 'cm';

        return new self(
            (float) $dimensions['length'],
            (float) $dimensions['width'],
            (float) $dimensions['height'],
            $unit
        );
    }

    public function toArray(): array
    {
        return [
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'unit' => $this->unit,
            'volume' => $this->getVolume(),
            'surface_area' => $this->getSurfaceArea()
        ];
    }

    public static function zero(string $unit = 'cm'): self
    {
        return new self(0.1, 0.1, 0.1, $unit); // Valores mínimos en lugar de cero
    }
}