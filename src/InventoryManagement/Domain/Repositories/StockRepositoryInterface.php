<?php

namespace Src\InventoryManagement\Domain\Repositories;

use Src\InventoryManagement\Domain\Entities\Stock;
use Src\InventoryManagement\Domain\ValueObjects\StockId;
use Src\InventoryManagement\Domain\ValueObjects\ProductId;
use Src\InventoryManagement\Domain\ValueObjects\WarehouseId;
use Illuminate\Support\Collection;

interface StockRepositoryInterface
{
    public function findById(StockId $id): ?Stock;

    public function save(Stock $stock): void;

    public function delete(StockId $id): bool;

    public function findAll(): Collection;

    public function findByProductId(ProductId $productId): Collection;

    public function findByWarehouseId(WarehouseId $warehouseId): Collection;

    public function findByProductAndWarehouse(ProductId $productId, WarehouseId $warehouseId): ?Stock;

    public function findLowStock(): Collection;

    public function findExpiredStock(): Collection;

    public function findExpiringSoon(int $days = 30): Collection;

    public function findActive(): Collection;

    public function findByBatchNumber(string $batchNumber): Collection;

    public function findByLocation(string $location): Collection;

    public function getTotalInventoryValue(): float;

    public function getInventoryValueByProduct(ProductId $productId): float;

    public function getInventoryValueByWarehouse(WarehouseId $warehouseId): float;

    public function getStockSummaryByProduct(): Collection;

    public function getStockSummaryByWarehouse(): Collection;

    public function getStockMovementHistory(StockId $stockId, int $limit = 50): Collection;

    public function exists(StockId $id): bool;

    public function existsForProductAndWarehouse(ProductId $productId, WarehouseId $warehouseId): bool;

    public function updateStockLevelsFromMovements(): void;

    public function getProductsNeedingReplenishment(): Collection;

    public function getStockAgingReport(): Collection;

    public function transferStock(
        StockId $sourceStockId,
        WarehouseId $targetWarehouseId,
        int $quantity,
        ?string $location = null
    ): StockId;

    public function adjustStock(StockId $stockId, int $quantity, string $reason, ?string $reference = null): void;
}