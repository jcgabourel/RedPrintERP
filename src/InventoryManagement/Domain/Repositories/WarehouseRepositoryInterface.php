<?php

namespace Src\InventoryManagement\Domain\Repositories;

use Src\InventoryManagement\Domain\Entities\Warehouse;
use Src\InventoryManagement\Domain\ValueObjects\WarehouseId;
use Illuminate\Support\Collection;

interface WarehouseRepositoryInterface
{
    public function findById(WarehouseId $id): ?Warehouse;

    public function save(Warehouse $warehouse): void;

    public function delete(WarehouseId $id): bool;

    public function findAll(): Collection;

    public function findActive(): Collection;

    public function findDefault(): ?Warehouse;

    public function findByCode(string $code): ?Warehouse;

    public function findByName(string $name): ?Warehouse;

    public function searchByName(string $name): Collection;

    public function findByCity(string $city): Collection;

    public function findByCountry(string $country): Collection;

    public function findByLocation(?string $city, ?string $state, ?string $country): Collection;

    public function getWarehousesWithCapacity(int $minCapacity = 0): Collection;

    public function getWarehousesByUtilization(float $minUtilization = 0, float $maxUtilization = 100): Collection;

    public function exists(WarehouseId $id): bool;

    public function existsWithCode(string $code, ?WarehouseId $excludeId = null): bool;

    public function existsWithName(string $name, ?WarehouseId $excludeId = null): bool;

    public function updateCurrentStock(WarehouseId $warehouseId): void;

    public function getTotalCapacity(): int;

    public function getTotalCurrentStock(): int;

    public function getAverageUtilization(): float;

    public function getWarehousesNeedingReplenishment(): Collection;

    public function getWarehousesNearLocation(float $latitude, float $longitude, float $radiusKm = 50): Collection;
}