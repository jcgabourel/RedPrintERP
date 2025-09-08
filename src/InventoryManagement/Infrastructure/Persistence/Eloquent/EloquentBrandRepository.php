<?php

namespace Src\InventoryManagement\Infrastructure\Persistence\Eloquent;

use Src\InventoryManagement\Domain\Entities\Brand;
use Src\InventoryManagement\Domain\Repositories\BrandRepositoryInterface;
use Src\InventoryManagement\Domain\ValueObjects\BrandId;
use App\Models\Brand as BrandModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EloquentBrandRepository implements BrandRepositoryInterface
{
    public function findById(BrandId $id): ?Brand
    {
        $model = BrandModel::find($id->getValue());

        if (!$model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function save(Brand $brand): void
    {
        DB::transaction(function () use ($brand) {
            $data = $this->mapToModelData($brand);

            if ($brand->getId()->isNull()) {
                // Crear nueva marca
                $model = new BrandModel();
                $model->fill($data);
                $model->save();

                // Actualizar la entidad con el ID generado
                $reflection = new \ReflectionClass($brand);
                $property = $reflection->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($brand, new BrandId($model->id));
            } else {
                // Actualizar marca existente
                $model = BrandModel::findOrFail($brand->getId()->getValue());
                $model->fill($data);
                $model->save();
            }
        });
    }

    public function delete(BrandId $id): bool
    {
        return DB::transaction(function () use ($id) {
            $model = BrandModel::findOrFail($id->getValue());

            // Verificar si tiene productos asociados
            if ($model->products()->count() > 0) {
                throw new InvalidArgumentException('Cannot delete brand with associated products');
            }

            return $model->delete();
        });
    }

    public function findAll(): Collection
    {
        return BrandModel::orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findActive(): Collection
    {
        return BrandModel::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByName(string $name): ?Brand
    {
        $model = BrandModel::where('name', $name)->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function searchByName(string $name): Collection
    {
        return BrandModel::where('name', 'like', "%{$name}%")
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByEmail(string $email): ?Brand
    {
        $model = BrandModel::where('contact_email', $email)->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function countProducts(BrandId $brandId): int
    {
        return BrandModel::findOrFail($brandId->getValue())
            ->products()
            ->count();
    }

    public function exists(BrandId $id): bool
    {
        return BrandModel::where('id', $id->getValue())->exists();
    }

    public function existsWithName(string $name, ?BrandId $excludeId = null): bool
    {
        $query = BrandModel::where('name', $name);

        if ($excludeId && !$excludeId->isNull()) {
            $query->where('id', '!=', $excludeId->getValue());
        }

        return $query->exists();
    }

    public function existsWithEmail(string $email, ?BrandId $excludeId = null): bool
    {
        $query = BrandModel::where('contact_email', $email);

        if ($excludeId && !$excludeId->isNull()) {
            $query->where('id', '!=', $excludeId->getValue());
        }

        return $query->exists();
    }

    public function getBrandsWithProductCount(int $minProducts = 0): Collection
    {
        return BrandModel::withCount('products')
            ->having('products_count', '>=', $minProducts)
            ->orderBy('products_count', 'desc')
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function getTopBrands(int $limit = 10): Collection
    {
        return BrandModel::withCount('products')
            ->orderBy('products_count', 'desc')
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function updateProductCount(BrandId $brandId): void
    {
        DB::transaction(function () use ($brandId) {
            $model = BrandModel::findOrFail($brandId->getValue());
            $productCount = $model->products()->count();
            
            $model->product_count = $productCount;
            $model->save();
        });
    }

    private function mapToEntity(BrandModel $model): Brand
    {
        return new Brand(
            new BrandId($model->id),
            $model->name,
            $model->description,
            $model->website,
            $model->contact_email,
            $model->contact_phone,
            $model->logo_url,
            (bool) $model->is_active,
            $model->product_count,
            \DateTimeImmutable::createFromMutable($model->created_at),
            \DateTimeImmutable::createFromMutable($model->updated_at)
        );
    }

    private function mapToModelData(Brand $entity): array
    {
        return [
            'id' => $entity->getId()->getValue(),
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'website' => $entity->getWebsite(),
            'contact_email' => $entity->getContactEmail(),
            'contact_phone' => $entity->getContactPhone(),
            'logo_url' => $entity->getLogoUrl(),
            'is_active' => $entity->isActive(),
            'product_count' => $entity->getProductCount(),
            'created_at' => $entity->getCreatedAt(),
            'updated_at' => $entity->getUpdatedAt()
        ];
    }
}