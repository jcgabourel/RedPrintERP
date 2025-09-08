<?php

namespace Src\InventoryManagement\Domain\Repositories;

use Src\InventoryManagement\Domain\Entities\Brand;
use Src\InventoryManagement\Domain\ValueObjects\BrandId;
use Illuminate\Support\Collection;

interface BrandRepositoryInterface
{
    public function findById(BrandId $id): ?Brand;

    public function save(Brand $brand): void;

    public function delete(BrandId $id): bool;

    public function findAll(): Collection;

    public function findActive(): Collection;

    public function findByName(string $name): ?Brand;

    public function searchByName(string $name): Collection;

    public function findByEmail(string $email): ?Brand;

    public function countProducts(BrandId $brandId): int;

    public function exists(BrandId $id): bool;

    public function existsWithName(string $name, ?BrandId $excludeId = null): bool;

    public function existsWithEmail(string $email, ?BrandId $excludeId = null): bool;

    public function getBrandsWithProductCount(int $minProducts = 0): Collection;

    public function getTopBrands(int $limit = 10): Collection;

    public function updateProductCount(BrandId $brandId): void;
}