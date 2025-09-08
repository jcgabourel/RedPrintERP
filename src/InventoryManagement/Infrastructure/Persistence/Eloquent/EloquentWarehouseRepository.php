<?php

namespace Src\InventoryManagement\Infrastructure\Persistence\Eloquent;

use Src\InventoryManagement\Domain\Entities\Warehouse;
use Src\InventoryManagement\Domain\Repositories\WarehouseRepositoryInterface;
use Src\InventoryManagement\Domain\ValueObjects\WarehouseId;
use Src\InventoryManagement\Domain\ValueObjects\Dimensions;
use Src\InventoryManagement\Domain\ValueObjects\StockQuantity;
use App\Models\InventoryWarehouse as WarehouseModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EloquentWarehouseRepository implements WarehouseRepositoryInterface
{
    public function findById(WarehouseId $id): ?Warehouse
    {
        $model = WarehouseModel::find($id->getValue());

        if (!$model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function save(Warehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {
            $data = $this->mapToModelData($warehouse);

            if ($warehouse->getId()->isNull()) {
                // Crear nuevo almacén
                $model = new WarehouseModel();
                $model->fill($data);
                $model->save();

                // Actualizar la entidad con el ID generado
                $reflection = new \ReflectionClass($warehouse);
                $property = $reflection->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($warehouse, new WarehouseId($model->id));
            } else {
                // Actualizar almacén existente
                $model = WarehouseModel::findOrFail($warehouse->getId()->getValue());
                $model->fill($data);
                $model->save();
            }
        });
    }

    public function delete(WarehouseId $id): bool
    {
        return DB::transaction(function () use ($id) {
            $model = WarehouseModel::findOrFail($id->getValue());

            // Verificar si tiene stock asociado
            if ($model->stocks()->count() > 0) {
                throw new InvalidArgumentException('Cannot delete warehouse with associated stock');
            }

            return $model->delete();
        });
    }

    public function findAll(): Collection
    {
        return WarehouseModel::orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findActive(): Collection
    {
        return WarehouseModel::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findDefault(): ?Warehouse
    {
        $model = WarehouseModel::where('is_default', true)
            ->where('is_active', true)
            ->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function findByCode(string $code): ?Warehouse
    {
        $model = WarehouseModel::where('code', $code)->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function findByName(string $name): ?Warehouse
    {
        $model = WarehouseModel::where('name', $name)->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function searchByName(string $name): Collection
    {
        return WarehouseModel::where('name', 'like', "%{$name}%")
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByCity(string $city): Collection
    {
        return WarehouseModel::where('city', $city)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByCountry(string $country): Collection
    {
        return WarehouseModel::where('country', $country)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByLocation(?string $city, ?string $state, ?string $country): Collection
    {
        $query = WarehouseModel::where('is_active', true);

        if ($city) {
            $query->where('city', $city);
        }

        if ($state) {
            $query->where('state', $state);
        }

        if ($country) {
            $query->where('country', $country);
        }

        return $query->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function getWarehousesWithCapacity(int $minCapacity = 0): Collection
    {
        return WarehouseModel::where('capacity', '>=', $minCapacity)
            ->where('is_active', true)
            ->orderBy('capacity', 'desc')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function getWarehousesByUtilization(float $minUtilization = 0, float $maxUtilization = 100): Collection
    {
        return WarehouseModel::where('is_active', true)
            ->get()
            ->map(fn($model) => $this->mapToEntity($model))
            ->filter(function (Warehouse $warehouse) use ($minUtilization, $maxUtilization) {
                $utilization = $warehouse->getUtilizationPercentage();
                return $utilization >= $minUtilization && $utilization <= $maxUtilization;
            })
            ->sortByDesc(fn(Warehouse $warehouse) => $warehouse->getUtilizationPercentage());
    }

    public function exists(WarehouseId $id): bool
    {
        return WarehouseModel::where('id', $id->getValue())->exists();
    }

    public function existsWithCode(string $code, ?WarehouseId $excludeId = null): bool
    {
        $query = WarehouseModel::where('code', $code);

        if ($excludeId && !$excludeId->isNull()) {
            $query->where('id', '!=', $excludeId->getValue());
        }

        return $query->exists();
    }

    public function existsWithName(string $name, ?WarehouseId $excludeId = null): bool
    {
        $query = WarehouseModel::where('name', $name);

        if ($excludeId && !$excludeId->isNull()) {
            $query->where('id', '!=', $excludeId->getValue());
        }

        return $query->exists();
    }

    public function updateCurrentStock(WarehouseId $warehouseId): void
    {
        DB::transaction(function () use ($warehouseId) {
            $model = WarehouseModel::findOrFail($warehouseId->getValue());
            $currentStock = $model->stocks()->sum('quantity');
            
            $model->current_stock = $currentStock;
            $model->save();
        });
    }

    public function getTotalCapacity(): int
    {
        return WarehouseModel::where('is_active', true)
            ->sum('capacity');
    }

    public function getTotalCurrentStock(): int
    {
        return WarehouseModel::where('is_active', true)
            ->sum('current_stock');
    }

    public function getAverageUtilization(): float
    {
        $totalCapacity = $this->getTotalCapacity();
        if ($totalCapacity === 0) {
            return 0.0;
        }

        $totalCurrentStock = $this->getTotalCurrentStock();
        return ($totalCurrentStock / $totalCapacity) * 100;
    }

    public function getWarehousesNeedingReplenishment(): Collection
    {
        return WarehouseModel::where('is_active', true)
            ->get()
            ->map(fn($model) => $this->mapToEntity($model))
            ->filter(fn(Warehouse $warehouse) => $warehouse->getUtilizationPercentage() >= 90)
            ->sortByDesc(fn(Warehouse $warehouse) => $warehouse->getUtilizationPercentage());
    }

    public function getWarehousesNearLocation(float $latitude, float $longitude, float $radiusKm = 50): Collection
    {
        // Fórmula de Haversine para calcular distancia en kilómetros
        $distanceFormula = "
            6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )
        ";

        return WarehouseModel::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("*, {$distanceFormula} as distance", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    private function mapToEntity(WarehouseModel $model): Warehouse
    {
        $dimensions = new Dimensions(
            (float) $model->length,
            (float) $model->width,
            (float) $model->height,
            $model->dimension_unit
        );

        return new Warehouse(
            new WarehouseId($model->id),
            $model->name,
            $model->code,
            $model->address,
            $model->city,
            $model->state,
            $model->country,
            $model->postal_code,
            $model->latitude,
            $model->longitude,
            $model->contact_person,
            $model->contact_phone,
            $model->contact_email,
            $dimensions,
            new StockQuantity($model->capacity),
            new StockQuantity($model->current_stock),
            (bool) $model->is_active,
            (bool) $model->is_default,
            \DateTimeImmutable::createFromMutable($model->created_at),
            \DateTimeImmutable::createFromMutable($model->updated_at)
        );
    }

    private function mapToModelData(Warehouse $entity): array
    {
        return [
            'id' => $entity->getId()->getValue(),
            'name' => $entity->getName(),
            'code' => $entity->getCode(),
            'address' => $entity->getAddress(),
            'city' => $entity->getCity(),
            'state' => $entity->getState(),
            'country' => $entity->getCountry(),
            'postal_code' => $entity->getPostalCode(),
            'latitude' => $entity->getLatitude(),
            'longitude' => $entity->getLongitude(),
            'contact_person' => $entity->getContactPerson(),
            'contact_phone' => $entity->getContactPhone(),
            'contact_email' => $entity->getContactEmail(),
            'length' => $entity->getDimensions()->getLength(),
            'width' => $entity->getDimensions()->getWidth(),
            'height' => $entity->getDimensions()->getHeight(),
            'dimension_unit' => $entity->getDimensions()->getUnit(),
            'capacity' => $entity->getCapacity()->getValue(),
            'current_stock' => $entity->getCurrentStock()->getValue(),
            'is_active' => $entity->isActive(),
            'is_default' => $entity->isDefault(),
            'created_at' => $entity->getCreatedAt(),
            'updated_at' => $entity->getUpdatedAt()
        ];
    }
}