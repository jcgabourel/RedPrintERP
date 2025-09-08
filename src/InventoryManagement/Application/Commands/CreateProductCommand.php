<?php

namespace Src\InventoryManagement\Application\Commands;

use Src\InventoryManagement\Domain\Entities\Product;
use Src\InventoryManagement\Domain\Entities\ProductBuilder;
use Src\InventoryManagement\Domain\Repositories\ProductRepositoryInterface;

class CreateProductCommand
{
    public function __construct(
        private string $name,
        private string $description,
        private string $sku,
        private float $price,
        private float $cost,
        private string $categoryId,
        private ?string $brandId,
        private string $unitId,
        private float $weight,
        private float $width,
        private float $height,
        private float $depth,
        private int $minStock,
        private int $maxStock
    ) {}

    public function execute(): Product
    {
        $builder = new ProductBuilder($this->name, $this->sku, $this->price, $this->cost);
        
        $product = $builder
            ->withDescription($this->description)
            ->withCategoryId((int) $this->categoryId)
            ->withBrandId($this->brandId ? (int) $this->brandId : null)
            ->withUnitId((int) $this->unitId)
            ->withWeight($this->weight)
            ->withDimensions($this->width, $this->height, $this->depth)
            ->withMinStock($this->minStock)
            ->withMaxStock($this->maxStock)
            ->build();

        $repository = app(ProductRepositoryInterface::class);
        $repository->save($product);

        return $product;
    }
}