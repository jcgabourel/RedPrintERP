<?php

namespace Src\InventoryManagement\Domain\Repositories;

use Src\InventoryManagement\Domain\Entities\Product;
use Src\InventoryManagement\Domain\ValueObjects\ProductId;
use Src\InventoryManagement\Domain\ValueObjects\CategoryId;
use Src\InventoryManagement\Domain\ValueObjects\BrandId;
use Src\InventoryManagement\Domain\ValueObjects\Sku;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function findById(ProductId $id): ?Product;

    public function findBySku(Sku $sku): ?Product;

    public function save(Product $product): void;

    public function delete(ProductId $id): bool;

    public function findAll(): Collection;

    public function findActive(): Collection;

    public function findByCategoryId(CategoryId $categoryId): Collection;

    public function findByBrandId(BrandId $brandId): Collection;

    public function searchByName(string $name): Collection;

    public function searchByDescription(string $description): Collection;

    public function findByPriceRange(float $minPrice, float $maxPrice): Collection;

    public function findLowStock(int $threshold = 10): Collection;

    public function findOutOfStock(): Collection;

    public function getProductsWithTotalStock(): Collection;

    public function exists(ProductId $id): bool;

    public function existsWithSku(Sku $sku, ?ProductId $excludeId = null): bool;

    public function getTotalInventoryValue(): float;

    public function updateProductStockCount(ProductId $productId): void;

    public function paginate(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $categoryId = null,
        ?string $brandId = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        string $sortBy = 'name',
        string $sortOrder = 'asc'
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}