<?php

namespace Src\InventoryManagement\Application\Queries;

use Src\InventoryManagement\Domain\Repositories\ProductRepositoryInterface;

class GetProductStatsQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(): array
    {
        return [
            'total_products' => $this->productRepository->findAll()->count(),
            'active_products' => $this->productRepository->findActive()->count(),
            'low_stock_count' => $this->productRepository->findLowStock()->count(),
            'out_of_stock_count' => $this->productRepository->findOutOfStock()->count(),
            'total_inventory_value' => $this->productRepository->getTotalInventoryValue(),
        ];
    }
}