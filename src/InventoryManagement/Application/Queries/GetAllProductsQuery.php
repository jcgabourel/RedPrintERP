<?php

namespace Src\InventoryManagement\Application\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Src\InventoryManagement\Domain\Repositories\ProductRepositoryInterface;

class GetAllProductsQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $categoryId = null,
        ?string $brandId = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        string $sortBy = 'name',
        string $sortOrder = 'asc'
    ): LengthAwarePaginator {
        return $this->productRepository->paginate(
            $page,
            $perPage,
            $search,
            $categoryId,
            $brandId,
            $minPrice,
            $maxPrice,
            $sortBy,
            $sortOrder
        );
    }
}