<?php

namespace Src\InventoryManagement\Domain\Repositories;

use Src\InventoryManagement\Domain\Entities\Unit;
use Src\InventoryManagement\Domain\ValueObjects\UnitId;
use Illuminate\Support\Collection;

interface UnitRepositoryInterface
{
    public function findById(UnitId $id): ?Unit;

    public function save(Unit $unit): void;

    public function delete(UnitId $id): bool;

    public function findAll(): Collection;

    public function findActive(): Collection;

    public function findByType(string $type): Collection;

    public function findBaseUnits(): Collection;

    public function findByName(string $name): ?Unit;

    public function findByAbbreviation(string $abbreviation): ?Unit;

    public function searchByName(string $name): Collection;

    public function exists(UnitId $id): bool;

    public function existsWithName(string $name, ?UnitId $excludeId = null): bool;

    public function existsWithAbbreviation(string $abbreviation, ?UnitId $excludeId = null): bool;

    public function getUnitsByUsage(int $minUsage = 0): Collection;

    public function getConversionOptions(UnitId $unitId): Collection;

    public function updateUsageCount(UnitId $unitId): void;

    public function getBaseUnitForType(string $type): ?Unit;

    public function validateConversion(UnitId $sourceUnitId, UnitId $targetUnitId): bool;
}