<?php

namespace Src\InventoryManagement\Infrastructure\Persistence\Eloquent;

use Src\InventoryManagement\Domain\Entities\Stock;
use Src\InventoryManagement\Domain\Repositories\StockRepositoryInterface;
use Src\InventoryManagement\Domain\ValueObjects\StockId;
use Src\InventoryManagement\Domain\ValueObjects\ProductId;
use Src\InventoryManagement\Domain\ValueObjects\WarehouseId;
use Src\InventoryManagement\Domain\ValueObjects\StockQuantity;
use Src\InventoryManagement\Domain\ValueObjects\Price;
use App\Models\Stock as StockModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EloquentStockRepository implements StockRepositoryInterface
{
    public function findById(StockId $id): ?Stock
    {
        $model = StockModel::with(['product', 'warehouse'])->find($id->getValue());

        if (!$model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function save(Stock $stock): void
    {
        DB::transaction(function () use ($stock) {
            $data = $this->mapToModelData($stock);

            if ($stock->getId()->isNull()) {
                // Crear nuevo stock
                $model = new StockModel();
                $model->fill($data);
                $model->save();

                // Actualizar la entidad con el ID generado
                $reflection = new \ReflectionClass($stock);
                $property = $reflection->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($stock, new StockId($model->id));

                // Actualizar contador de stock en el almacén
                $this->updateWarehouseStock($model->warehouse_id);
            } else {
                // Actualizar stock existente
                $model = StockModel::findOrFail($stock->getId()->getValue());
                $oldWarehouseId = $model->warehouse_id;
                
                $model->fill($data);
                $model->save();

                // Actualizar contadores de stock si cambió el almacén
                if ($oldWarehouseId != $model->warehouse_id) {
                    $this->updateWarehouseStock($oldWarehouseId);
                    $this->updateWarehouseStock($model->warehouse_id);
                } else {
                    $this->updateWarehouseStock($model->warehouse_id);
                }
            }
        });
    }

    public function delete(StockId $id): bool
    {
        return DB::transaction(function () use ($id) {
            $model = StockModel::findOrFail($id->getValue());
            $warehouseId = $model->warehouse_id;
            
            $deleted = $model->delete();

            if ($deleted) {
                $this->updateWarehouseStock($warehouseId);
            }

            return $deleted;
        });
    }

    public function findAll(): Collection
    {
        return StockModel::with(['product', 'warehouse'])
            ->orderBy('product_id')
            ->orderBy('warehouse_id')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByProductId(ProductId $productId): Collection
    {
        return StockModel::with(['product', 'warehouse'])
            ->where('product_id', $productId->getValue())
            ->orderBy('warehouse_id')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByWarehouseId(WarehouseId $warehouseId): Collection
    {
        return StockModel::with(['product', 'warehouse'])
            ->where('warehouse_id', $warehouseId->getValue())
            ->orderBy('product_id')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByProductAndWarehouse(ProductId $productId, WarehouseId $warehouseId): ?Stock
    {
        $model = StockModel::with(['product', 'warehouse'])
            ->where('product_id', $productId->getValue())
            ->where('warehouse_id', $warehouseId->getValue())
            ->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function findLowStock(): Collection
    {
        return StockModel::with(['product', 'warehouse'])
            ->where('quantity', '<=', DB::raw('minimum_stock_level'))
            ->where('quantity', '>', 0)
            ->where('is_active', true)
            ->orderBy('quantity')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findExpiredStock(): Collection
    {
        return StockModel::with(['product', 'warehouse'])
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<', now())
            ->where('quantity', '>', 0)
            ->where('is_active', true)
            ->orderBy('expiration_date')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findExpiringSoon(int $days = 30): Collection
    {
        $threshold = now()->addDays($days);

        return StockModel::with(['product', 'warehouse'])
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', $threshold)
            ->where('expiration_date', '>=', now())
            ->where('quantity', '>', 0)
            ->where('is_active', true)
            ->orderBy('expiration_date')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findActive(): Collection
    {
        return StockModel::with(['product', 'warehouse'])
            ->where('is_active', true)
            ->orderBy('product_id')
            ->orderBy('warehouse_id')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByBatchNumber(string $batchNumber): Collection
    {
        return StockModel::with(['product', 'warehouse'])
            ->where('batch_number', $batchNumber)
            ->orderBy('expiration_date')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByLocation(string $location): Collection
    {
        return StockModel::with(['product', 'warehouse'])
            ->where('location', 'like', "%{$location}%")
            ->orderBy('product_id')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function getTotalInventoryValue(): float
    {
        return StockModel::where('is_active', true)
            ->sum(DB::raw('quantity * unit_cost'));
    }

    public function getInventoryValueByProduct(ProductId $productId): float
    {
        return StockModel::where('product_id', $productId->getValue())
            ->where('is_active', true)
            ->sum(DB::raw('quantity * unit_cost'));
    }

    public function getInventoryValueByWarehouse(WarehouseId $warehouseId): float
    {
        return StockModel::where('warehouse_id', $warehouseId->getValue())
            ->where('is_active', true)
            ->sum(DB::raw('quantity * unit_cost'));
    }

    public function getStockSummaryByProduct(): Collection
    {
        return StockModel::with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(quantity * unit_cost) as total_value')
            ->where('is_active', true)
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'total_quantity' => $item->total_quantity,
                    'total_value' => $item->total_value
                ];
            });
    }

    public function getStockSummaryByWarehouse(): Collection
    {
        return StockModel::with('warehouse')
            ->selectRaw('warehouse_id, SUM(quantity) as total_quantity, SUM(quantity * unit_cost) as total_value')
            ->where('is_active', true)
            ->groupBy('warehouse_id')
            ->orderBy('total_quantity', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'warehouse_id' => $item->warehouse_id,
                    'warehouse_name' => $item->warehouse->name,
                    'total_quantity' => $item->total_quantity,
                    'total_value' => $item->total_value
                ];
            });
    }

    public function getStockMovementHistory(StockId $stockId, int $limit = 50): Collection
    {
        // Esta implementación asume que tienes una tabla de movimientos de stock
        // Se implementaría con un modelo StockMovement
        return collect();
    }

    public function exists(StockId $id): bool
    {
        return StockModel::where('id', $id->getValue())->exists();
    }

    public function existsForProductAndWarehouse(ProductId $productId, WarehouseId $warehouseId): bool
    {
        return StockModel::where('product_id', $productId->getValue())
            ->where('warehouse_id', $warehouseId->getValue())
            ->exists();
    }

    public function updateStockLevelsFromMovements(): void
    {
        // Implementación para sincronizar stock con movimientos
        // Esto actualizaría las cantidades basado en los movimientos registrados
    }

    public function getProductsNeedingReplenishment(): Collection
    {
        return StockModel::with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(minimum_stock_level) as total_minimum')
            ->where('is_active', true)
            ->groupBy('product_id')
            ->havingRaw('SUM(quantity) < SUM(minimum_stock_level)')
            ->get()
            ->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'current_quantity' => $item->total_quantity,
                    'minimum_required' => $item->total_minimum,
                    'needed_quantity' => $item->total_minimum - $item->total_quantity
                ];
            });
    }

    public function getStockAgingReport(): Collection
    {
        return StockModel::with(['product', 'warehouse'])
            ->where('quantity', '>', 0)
            ->where('is_active', true)
            ->orderBy('created_at')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function transferStock(
        StockId $sourceStockId,
        WarehouseId $targetWarehouseId,
        int $quantity,
        ?string $location = null
    ): StockId {
        return DB::transaction(function () use ($sourceStockId, $targetWarehouseId, $quantity, $location) {
            $sourceStock = $this->findById($sourceStockId);
            if (!$sourceStock) {
                throw new InvalidArgumentException('Source stock not found');
            }

            if ($sourceStock->getQuantity()->getValue() < $quantity) {
                throw new InvalidArgumentException('Insufficient stock for transfer');
            }

            // Remover stock del origen
            $sourceStock->removeStock(new StockQuantity($quantity));
            $this->save($sourceStock);

            // Buscar o crear stock en el destino
            $targetStock = $this->findByProductAndWarehouse(
                $sourceStock->getProductId(),
                $targetWarehouseId
            );

            if ($targetStock) {
                $targetStock->addStock(new StockQuantity($quantity), $sourceStock->getUnitCost());
            } else {
                $targetStock = Stock::create(
                    $sourceStock->getProductId(),
                    $targetWarehouseId,
                    new StockQuantity($quantity),
                    $sourceStock->getMinimumStockLevel(),
                    $sourceStock->getMaximumStockLevel(),
                    $sourceStock->getUnitCost(),
                    $location,
                    $sourceStock->getBatchNumber(),
                    $sourceStock->getExpirationDate()
                );
            }

            $this->save($targetStock);

            return $targetStock->getId();
        });
    }

    public function adjustStock(StockId $stockId, int $quantity, string $reason, ?string $reference = null): void
    {
        DB::transaction(function () use ($stockId, $quantity, $reason, $reference) {
            $stock = $this->findById($stockId);
            if (!$stock) {
                throw new InvalidArgumentException('Stock not found');
            }

            if ($quantity > 0) {
                $stock->addStock(new StockQuantity($quantity), $stock->getUnitCost());
            } else {
                $stock->removeStock(new StockQuantity(abs($quantity)));
            }

            $this->save($stock);

            // Registrar el movimiento de ajuste (asumiendo que tienes una tabla de movimientos)
            // StockMovement::create([...]);
        });
    }

    private function updateWarehouseStock(int $warehouseId): void
    {
        $totalStock = StockModel::where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->sum('quantity');

        \App\Models\Warehouse::where('id', $warehouseId)
            ->update(['current_stock' => $totalStock]);
    }

    private function mapToEntity(StockModel $model): Stock
    {
        return new Stock(
            new StockId($model->id),
            new ProductId($model->product_id),
            new WarehouseId($model->warehouse_id),
            new StockQuantity($model->quantity),
            new StockQuantity($model->minimum_stock_level),
            new StockQuantity($model->maximum_stock_level),
            new Price($model->unit_cost),
            new Price($model->total_value),
            $model->location,
            $model->batch_number,
            $model->expiration_date ? \DateTimeImmutable::createFromMutable($model->expiration_date) : null,
            (bool) $model->is_active,
            \DateTimeImmutable::createFromMutable($model->created_at),
            \DateTimeImmutable::createFromMutable($model->updated_at)
        );
    }

    private function mapToModelData(Stock $entity): array
    {
        return [
            'id' => $entity->getId()->getValue(),
            'product_id' => $entity->getProductId()->getValue(),
            'warehouse_id' => $entity->getWarehouseId()->getValue(),
            'quantity' => $entity->getQuantity()->getValue(),
            'minimum_stock_level' => $entity->getMinimumStockLevel()->getValue(),
            'maximum_stock_level' => $entity->getMaximumStockLevel()->getValue(),
            'unit_cost' => $entity->getUnitCost()->getValue(),
            'total_value' => $entity->getTotalValue()->getValue(),
            'location' => $entity->getLocation(),
            'batch_number' => $entity->getBatchNumber(),
            'expiration_date' => $entity->getExpirationDate(),
            'is_active' => $entity->isActive(),
            'created_at' => $entity->getCreatedAt(),
            'updated_at' => $entity->getUpdatedAt()
        ];
    }
}