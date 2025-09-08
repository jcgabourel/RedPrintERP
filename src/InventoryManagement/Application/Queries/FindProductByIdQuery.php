<?php

namespace Src\InventoryManagement\Application\Queries;

use Src\InventoryManagement\Domain\Entities\Product;
use Src\InventoryManagement\Domain\Repositories\ProductRepositoryInterface;
use Src\InventoryManagement\Domain\ValueObjects\ProductId;

class FindProductByIdQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(ProductId $productId): ?Product
    {
        return $this->productRepository->findById($productId);
    }
}