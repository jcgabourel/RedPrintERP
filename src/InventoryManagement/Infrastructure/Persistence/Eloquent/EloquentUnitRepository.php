<?php

namespace Src\InventoryManagement\Infrastructure\Persistence\Eloquent;

use Src\InventoryManagement\Domain\Entities\Unit;
use Src\InventoryManagement\Domain\Repositories\UnitRepositoryInterface;
use Src\InventoryManagement\Domain\ValueObjects\UnitId;
use App\Models\Unit as UnitModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EloquentUnitRepository implements UnitRepositoryInterface
{
    public function findById(UnitId $id): ?Unit
    {
        $model = UnitModel::find($id->getValue());

        if (!$model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function save(Unit $unit): void
    {
        DB::transaction(function () use ($unit) {
            $data = $this->mapToModelData($unit);

            if ($unit->getId()->isNull()) {
                // Crear nueva unidad
                $model = new UnitModel();
                $model->fill($data);
                $model->save();

                // Actualizar la entidad con el ID generado
                $reflection = new \ReflectionClass($unit);
                $property = $reflection->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($unit, new UnitId($model->id));
            } else {
                // Actualizar unidad existente
                $model = UnitModel::findOrFail($unit->getId()->getValue());
                $model->fill($data);
                $model->save();
            }
        });
    }

    public function delete(UnitId $id): bool
    {
        return DB::transaction(function () use ($id) {
            $model = UnitModel::findOrFail($id->getValue());

            // Verificar si está en uso por productos
            if ($model->products()->count() > 0) {
                throw new InvalidArgumentException('Cannot delete unit that is used by products');
            }

            return $model->delete();
        });
    }

    public function findAll(): Collection
    {
        return UnitModel::orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findActive(): Collection
    {
        return UnitModel::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByType(string $type): Collection
    {
        return UnitModel::where('type', $type)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findBaseUnits(): Collection
    {
        return UnitModel::where('is_base_unit', true)
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByName(string $name): ?Unit
    {
        $model = UnitModel::where('name', $name)->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function findByAbbreviation(string $abbreviation): ?Unit
    {
        $model = UnitModel::where('abbreviation', $abbreviation)->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function searchByName(string $name): Collection
    {
        return UnitModel::where('name', 'like', "%{$name}%")
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function exists(UnitId $id): bool
    {
        return UnitModel::where('id', $id->getValue())->exists();
    }

    public function existsWithName(string $name, ?UnitId $excludeId = null): bool
    {
        $query = UnitModel::where('name', $name);

        if ($excludeId && !$excludeId->isNull()) {
            $query->where('id', '!=', $excludeId->getValue());
        }

        return $query->exists();
    }

    public function existsWithAbbreviation(string $abbreviation, ?UnitId $excludeId = null): bool
    {
        $query = UnitModel::where('abbreviation', $abbreviation);

        if ($excludeId && !$excludeId->isNull()) {
            $query->where('id', '!=', $excludeId->getValue());
        }

        return $query->exists();
    }

    public function getUnitsByUsage(int $minUsage = 0): Collection
    {
        return UnitModel::where('usage_count', '>=', $minUsage)
            ->orderBy('usage_count', 'desc')
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function getConversionOptions(UnitId $unitId): Collection
    {
        $unit = $this->findById($unitId);
        if (!$unit) {
            return collect();
        }

        return $this->findByType($unit->getType());
    }

    public function updateUsageCount(UnitId $unitId): void
    {
        DB::transaction(function () use ($unitId) {
            $model = UnitModel::findOrFail($unitId->getValue());
            $usageCount = $model->products()->count();
            
            $model->usage_count = $usageCount;
            $model->save();
        });
    }

    public function getBaseUnitForType(string $type): ?Unit
    {
        $model = UnitModel::where('type', $type)
            ->where('is_base_unit', true)
            ->where('is_active', true)
            ->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function validateConversion(UnitId $sourceUnitId, UnitId $targetUnitId): bool
    {
        $sourceUnit = $this->findById($sourceUnitId);
        $targetUnit = $this->findById($targetUnitId);

        if (!$sourceUnit || !$targetUnit) {
            return false;
        }

        return $sourceUnit->getType() === $targetUnit->getType();
    }

    private function mapToEntity(UnitModel $model): Unit
    {
        return new Unit(
            new UnitId($model->id),
            $model->name,
            $model->abbreviation,
            $model->type,
            (float) $model->conversion_factor,
            (bool) $model->is_base_unit,
            (bool) $model->is_active,
            $model->usage_count,
            \DateTimeImmutable::createFromMutable($model->created_at),
            \DateTimeImmutable::createFromMutable($model->updated_at)
        );
    }

    private function mapToModelData(Unit $entity): array
    {
        return [
            'id' => $entity->getId()->getValue(),
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation(),
            'type' => $entity->getType(),
            'conversion_factor' => $entity->getConversionFactor(),
            'is_base_unit' => $entity->isBaseUnit(),
            'is_active' => $entity->isActive(),
            'usage_count' => $entity->getUsageCount(),
            'created_at' => $entity->getCreatedAt(),
            'updated_at' => $entity->getUpdatedAt()
        ];
    }
}