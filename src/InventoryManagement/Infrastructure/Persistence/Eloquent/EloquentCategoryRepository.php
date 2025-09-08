<?php

namespace Src\InventoryManagement\Infrastructure\Persistence\Eloquent;

use Src\InventoryManagement\Domain\Entities\Category;
use Src\InventoryManagement\Domain\Repositories\CategoryRepositoryInterface;
use Src\InventoryManagement\Domain\ValueObjects\CategoryId;
use App\Models\Category as CategoryModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function findById(CategoryId $id): ?Category
    {
        $model = CategoryModel::find($id->getValue());

        if (!$model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function save(Category $category): void
    {
        DB::transaction(function () use ($category) {
            $data = $category->toArray();

            if ($category->getId()->isNull()) {
                // Crear nueva categoría
                $model = new CategoryModel();
                $model->fill($data);
                $model->save();

                // Actualizar path y level después de crear
                $this->updateCategoryHierarchy($model);
                
                // Actualizar la entidad con el ID generado
                $reflection = new \ReflectionClass($category);
                $property = $reflection->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($category, new CategoryId($model->id));
            } else {
                // Actualizar categoría existente
                $model = CategoryModel::findOrFail($category->getId()->getValue());
                
                // Guardar cambios antes de actualizar jerarquía
                $oldParentId = $model->parent_id;
                $model->fill($data);
                $model->save();

                // Si cambió el parent_id, actualizar jerarquía
                if ($oldParentId != $model->parent_id) {
                    $this->updateCategoryHierarchy($model);
                }
            }
        });
    }

    public function delete(CategoryId $id): bool
    {
        return DB::transaction(function () use ($id) {
            $model = CategoryModel::findOrFail($id->getValue());

            // Verificar si tiene productos
            if ($model->products()->count() > 0) {
                throw new InvalidArgumentException('Cannot delete category with associated products');
            }

            // Verificar si tiene subcategorías
            if ($model->children()->count() > 0) {
                throw new InvalidArgumentException('Cannot delete category with subcategories');
            }

            return $model->delete();
        });
    }

    public function findAll(): Collection
    {
        return CategoryModel::orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByParentId(?CategoryId $parentId): Collection
    {
        $parentIdValue = $parentId?->getValue();

        return CategoryModel::where('parent_id', $parentIdValue)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findRootCategories(): Collection
    {
        return CategoryModel::whereNull('parent_id')
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByName(string $name): ?Category
    {
        $model = CategoryModel::where('name', $name)->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function searchByName(string $name): Collection
    {
        return CategoryModel::where('name', 'like', "%{$name}%")
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function getHierarchy(?CategoryId $parentId = null, int $level = 0): Collection
    {
        $parentIdValue = $parentId?->getValue();

        $categories = CategoryModel::where('parent_id', $parentIdValue)
            ->orderBy('name')
            ->get();

        $result = collect();

        foreach ($categories as $category) {
            $entity = $this->mapToEntity($category);
            $result->push([
                'category' => $entity,
                'level' => $level,
                'children' => $this->getHierarchy(new CategoryId($category->id), $level + 1)
            ]);
        }

        return $result;
    }

    public function getDescendants(CategoryId $categoryId): Collection
    {
        $category = CategoryModel::findOrFail($categoryId->getValue());
        
        return CategoryModel::where('path', 'like', "{$category->path}/%")
            ->orderBy('path')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function getAncestors(CategoryId $categoryId): Collection
    {
        $category = CategoryModel::findOrFail($categoryId->getValue());
        
        if (empty($category->path)) {
            return collect();
        }

        $ancestorIds = explode('/', trim($category->path, '/'));
        
        return CategoryModel::whereIn('id', $ancestorIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $ancestorIds) . ')')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function moveCategory(CategoryId $categoryId, ?CategoryId $newParentId): void
    {
        DB::transaction(function () use ($categoryId, $newParentId) {
            $category = CategoryModel::findOrFail($categoryId->getValue());
            
            // Verificar que no se mueva a sí misma o a un descendiente
            if ($newParentId && !$newParentId->isNull()) {
                $newParent = CategoryModel::findOrFail($newParentId->getValue());
                
                if ($category->id == $newParent->id) {
                    throw new InvalidArgumentException('Cannot move category to itself');
                }

                if (strpos($newParent->path, "{$category->path}/") === 0) {
                    throw new InvalidArgumentException('Cannot move category to its own descendant');
                }
            }

            $category->parent_id = $newParentId?->getValue();
            $category->save();

            $this->updateCategoryHierarchy($category);
        });
    }

    public function countProductsInCategory(CategoryId $categoryId): int
    {
        return CategoryModel::findOrFail($categoryId->getValue())
            ->products()
            ->count();
    }

    public function countTotalProductsInHierarchy(CategoryId $categoryId): int
    {
        $category = CategoryModel::findOrFail($categoryId->getValue());
        
        $descendantIds = CategoryModel::where('path', 'like', "{$category->path}/%")
            ->pluck('id')
            ->push($category->id);

        return \App\Models\Product::whereIn('category_id', $descendantIds)
            ->count();
    }

    public function exists(CategoryId $id): bool
    {
        return CategoryModel::where('id', $id->getValue())->exists();
    }

    public function existsWithName(string $name, ?CategoryId $excludeId = null): bool
    {
        $query = CategoryModel::where('name', $name);

        if ($excludeId && !$excludeId->isNull()) {
            $query->where('id', '!=', $excludeId->getValue());
        }

        return $query->exists();
    }

    private function updateCategoryHierarchy(CategoryModel $model): void
    {
        // Calcular path y level
        if ($model->parent_id) {
            $parent = CategoryModel::findOrFail($model->parent_id);
            $model->path = $parent->path ? "{$parent->path}/{$model->id}" : (string) $model->id;
            $model->level = $parent->level + 1;
        } else {
            $model->path = (string) $model->id;
            $model->level = 0;
        }

        $model->save();

        // Actualizar todos los descendientes
        $this->updateDescendantsHierarchy($model);
    }

    private function updateDescendantsHierarchy(CategoryModel $parent): void
    {
        $children = CategoryModel::where('parent_id', $parent->id)->get();

        foreach ($children as $child) {
            $child->path = $parent->path ? "{$parent->path}/{$child->id}" : (string) $child->id;
            $child->level = $parent->level + 1;
            $child->save();

            $this->updateDescendantsHierarchy($child);
        }
    }

    private function mapToEntity(CategoryModel $model): Category
    {
        return new Category(
            new CategoryId($model->id),
            $model->name,
            $model->description,
            $model->parent_id ? new CategoryId($model->parent_id) : null,
            $model->level,
            $model->path,
            (bool) $model->is_active,
            $model->product_count,
            \DateTimeImmutable::createFromMutable($model->created_at),
            \DateTimeImmutable::createFromMutable($model->updated_at)
        );
    }

    private function mapToModel(Category $entity): array
    {
        return [
            'id' => $entity->getId()->getValue(),
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'parent_id' => $entity->getParentId()?->getValue(),
            'level' => $entity->getLevel(),
            'path' => $entity->getPath(),
            'is_active' => $entity->isActive(),
            'product_count' => $entity->getProductCount(),
            'created_at' => $entity->getCreatedAt(),
            'updated_at' => $entity->getUpdatedAt()
        ];
    }
}