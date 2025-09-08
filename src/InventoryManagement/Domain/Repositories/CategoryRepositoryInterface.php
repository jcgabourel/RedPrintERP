<?php

namespace Src\InventoryManagement\Domain\Repositories;

use Src\InventoryManagement\Domain\Entities\Category;
use Src\InventoryManagement\Domain\ValueObjects\CategoryId;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function findById(CategoryId $id): ?Category;

    public function save(Category $category): void;

    public function delete(CategoryId $id): bool;

    public function findAll(): Collection;

    public function findByParentId(?CategoryId $parentId): Collection;

    public function findRootCategories(): Collection;

    public function findByName(string $name): ?Category;

    public function searchByName(string $name): Collection;

    public function getHierarchy(?CategoryId $parentId = null, int $level = 0): Collection;

    public function getDescendants(CategoryId $categoryId): Collection;

    public function getAncestors(CategoryId $categoryId): Collection;

    public function moveCategory(CategoryId $categoryId, ?CategoryId $newParentId): void;

    public function countProductsInCategory(CategoryId $categoryId): int;

    public function countTotalProductsInHierarchy(CategoryId $categoryId): int;

    public function exists(CategoryId $id): bool;

    public function existsWithName(string $name, ?CategoryId $excludeId = null): bool;
}