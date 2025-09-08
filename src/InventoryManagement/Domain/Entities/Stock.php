<?php

namespace Src\InventoryManagement\Domain\Entities;

use Src\InventoryManagement\Domain\ValueObjects\StockId;
use Src\InventoryManagement\Domain\ValueObjects\ProductId;
use Src\InventoryManagement\Domain\ValueObjects\WarehouseId;
use Src\InventoryManagement\Domain\ValueObjects\StockQuantity;
use Src\InventoryManagement\Domain\ValueObjects\Price;
use InvalidArgumentException;

class Stock
{
    private StockId $id;
    private ProductId $productId;
    private WarehouseId $warehouseId;
    private StockQuantity $quantity;
    private StockQuantity $minimumStockLevel;
    private StockQuantity $maximumStockLevel;
    private Price $unitCost;
    private Price $totalValue;
    private ?string $location;
    private ?string $batchNumber;
    private ?\DateTimeImmutable $expirationDate;
    private bool $isActive;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        StockId $id,
        ProductId $productId,
        WarehouseId $warehouseId,
        StockQuantity $quantity,
        StockQuantity $minimumStockLevel,
        StockQuantity $maximumStockLevel,
        Price $unitCost,
        Price $totalValue,
        ?string $location,
        ?string $batchNumber,
        ?\DateTimeImmutable $expirationDate,
        bool $isActive,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->validateStockLevels($minimumStockLevel, $maximumStockLevel, $quantity);
        $this->validateLocation($location);
        $this->validateBatchNumber($batchNumber);

        $this->id = $id;
        $this->productId = $productId;
        $this->warehouseId = $warehouseId;
        $this->quantity = $quantity;
        $this->minimumStockLevel = $minimumStockLevel;
        $this->maximumStockLevel = $maximumStockLevel;
        $this->unitCost = $unitCost;
        $this->totalValue = $totalValue;
        $this->location = $location;
        $this->batchNumber = $batchNumber;
        $this->expirationDate = $expirationDate;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    private function validateStockLevels(StockQuantity $min, StockQuantity $max, StockQuantity $quantity): void
    {
        if ($min->isGreaterThan($max)) {
            throw new InvalidArgumentException('Minimum stock level cannot be greater than maximum stock level');
        }

        if ($quantity->isGreaterThan($max)) {
            throw new InvalidArgumentException('Quantity cannot exceed maximum stock level');
        }

        if ($quantity->isLessThan($min) && !$quantity->isZero()) {
            throw new InvalidArgumentException('Quantity cannot be below minimum stock level unless zero');
        }
    }

    private function validateLocation(?string $location): void
    {
        if ($location !== null && strlen($location) > 50) {
            throw new InvalidArgumentException('Location cannot exceed 50 characters');
        }
    }

    private function validateBatchNumber(?string $batchNumber): void
    {
        if ($batchNumber !== null) {
            if (strlen($batchNumber) > 30) {
                throw new InvalidArgumentException('Batch number cannot exceed 30 characters');
            }

            if (!preg_match('/^[A-Z0-9\-_]+$/', $batchNumber)) {
                throw new InvalidArgumentException('Batch number can only contain uppercase letters, numbers, hyphens, and underscores');
            }
        }
    }

    public function getId(): StockId
    {
        return $this->id;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getWarehouseId(): WarehouseId
    {
        return $this->warehouseId;
    }

    public function getQuantity(): StockQuantity
    {
        return $this->quantity;
    }

    public function getMinimumStockLevel(): StockQuantity
    {
        return $this->minimumStockLevel;
    }

    public function getMaximumStockLevel(): StockQuantity
    {
        return $this->maximumStockLevel;
    }

    public function getUnitCost(): Price
    {
        return $this->unitCost;
    }

    public function getTotalValue(): Price
    {
        return $this->totalValue;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getBatchNumber(): ?string
    {
        return $this->batchNumber;
    }

    public function getExpirationDate(): ?\DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function addStock(StockQuantity $quantity, Price $unitCost): void
    {
        $newQuantity = $this->quantity->add($quantity);
        
        if ($newQuantity->isGreaterThan($this->maximumStockLevel)) {
            throw new InvalidArgumentException('Cannot add stock: exceeds maximum stock level');
        }

        // Calcular el nuevo costo promedio ponderado
        $currentTotalValue = $this->totalValue->getValue();
        $newStockValue = $unitCost->getValue() * $quantity->getValue();
        $totalValue = $currentTotalValue + $newStockValue;
        $totalQuantity = $this->quantity->getValue() + $quantity->getValue();

        $newUnitCost = new Price($totalValue / $totalQuantity);

        $this->quantity = $newQuantity;
        $this->unitCost = $newUnitCost;
        $this->totalValue = new Price($totalValue);
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function removeStock(StockQuantity $quantity): void
    {
        $newQuantity = $this->quantity->subtract($quantity);
        
        if ($newQuantity->isLessThan($this->minimumStockLevel) && !$newQuantity->isZero()) {
            throw new InvalidArgumentException('Cannot remove stock: would fall below minimum stock level');
        }

        // Recalcular el valor total
        $remainingValue = $this->unitCost->getValue() * $newQuantity->getValue();

        $this->quantity = $newQuantity;
        $this->totalValue = new Price($remainingValue);
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function adjustStockLevels(StockQuantity $minLevel, StockQuantity $maxLevel): void
    {
        $this->validateStockLevels($minLevel, $maxLevel, $this->quantity);
        
        $this->minimumStockLevel = $minLevel;
        $this->maximumStockLevel = $maxLevel;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateUnitCost(Price $unitCost): void
    {
        $this->unitCost = $unitCost;
        $this->totalValue = new Price($unitCost->getValue() * $this->quantity->getValue());
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateLocation(?string $location): void
    {
        $this->validateLocation($location);
        $this->location = $location;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateBatchInfo(?string $batchNumber, ?\DateTimeImmutable $expirationDate): void
    {
        $this->validateBatchNumber($batchNumber);
        $this->batchNumber = $batchNumber;
        $this->expirationDate = $expirationDate;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function isBelowMinimum(): bool
    {
        return $this->quantity->isLessThan($this->minimumStockLevel) && !$this->quantity->isZero();
    }

    public function isAboveMaximum(): bool
    {
        return $this->quantity->isGreaterThan($this->maximumStockLevel);
    }

    public function isExpired(): bool
    {
        if ($this->expirationDate === null) {
            return false;
        }

        return $this->expirationDate < new \DateTimeImmutable();
    }

    public function willExpireWithinDays(int $days): bool
    {
        if ($this->expirationDate === null) {
            return false;
        }

        $threshold = new \DateTimeImmutable("+{$days} days");
        return $this->expirationDate <= $threshold;
    }

    public function getDaysUntilExpiration(): ?int
    {
        if ($this->expirationDate === null) {
            return null;
        }

        $now = new \DateTimeImmutable();
        if ($this->expirationDate < $now) {
            return -$this->expirationDate->diff($now)->days;
        }

        return $this->expirationDate->diff($now)->days;
    }

    public function getStockStatus(): string
    {
        if ($this->isBelowMinimum()) {
            return 'low';
        }

        if ($this->isAboveMaximum()) {
            return 'high';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        return 'normal';
    }

    public static function create(
        ProductId $productId,
        WarehouseId $warehouseId,
        StockQuantity $quantity,
        StockQuantity $minimumStockLevel,
        StockQuantity $maximumStockLevel,
        Price $unitCost,
        ?string $location = null,
        ?string $batchNumber = null,
        ?\DateTimeImmutable $expirationDate = null
    ): self {
        $id = new StockId(null);
        $totalValue = new Price($unitCost->getValue() * $quantity->getValue());

        return new self(
            $id,
            $productId,
            $warehouseId,
            $quantity,
            $minimumStockLevel,
            $maximumStockLevel,
            $unitCost,
            $totalValue,
            $location,
            $batchNumber,
            $expirationDate,
            true,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->getValue(),
            'product_id' => $this->productId->getValue(),
            'warehouse_id' => $this->warehouseId->getValue(),
            'quantity' => $this->quantity->getValue(),
            'minimum_stock_level' => $this->minimumStockLevel->getValue(),
            'maximum_stock_level' => $this->maximumStockLevel->getValue(),
            'unit_cost' => $this->unitCost->getValue(),
            'total_value' => $this->totalValue->getValue(),
            'location' => $this->location,
            'batch_number' => $this->batchNumber,
            'expiration_date' => $this->expirationDate?->format('Y-m-d'),
            'is_active' => $this->isActive,
            'stock_status' => $this->getStockStatus(),
            'days_until_expiration' => $this->getDaysUntilExpiration(),
            'is_expired' => $this->isExpired(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }

    public function calculateReplenishmentQuantity(): StockQuantity
    {
        if ($this->quantity->isGreaterThanOrEqual($this->maximumStockLevel)) {
            return new StockQuantity(0);
        }

        return $this->maximumStockLevel->subtract($this->quantity);
    }

    public function hasBatchInfo(): bool
    {
        return $this->batchNumber !== null || $this->expirationDate !== null;
    }

    public function requiresReplenishment(): bool
    {
        return $this->isBelowMinimum() || $this->quantity->isZero();
    }
}