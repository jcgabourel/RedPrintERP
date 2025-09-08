<?php

namespace Src\InventoryManagement\Domain\Entities;

use Src\InventoryManagement\Domain\ValueObjects\Dimensions;
use Src\InventoryManagement\Domain\ValueObjects\Price;
use Src\InventoryManagement\Domain\ValueObjects\ProductId;
use Src\InventoryManagement\Domain\ValueObjects\ProductName;
use Src\InventoryManagement\Domain\ValueObjects\ProductSlug;
use Src\InventoryManagement\Domain\ValueObjects\Sku;
use Src\InventoryManagement\Domain\ValueObjects\StockQuantity;
use Src\InventoryManagement\Domain\ValueObjects\Weight;

class ProductBuilder
{
    private ?ProductId $id = null;
    private Sku $sku;
    private ProductName $name;
    private ProductSlug $slug;
    private ?string $description = null;
    private ?string $shortDescription = null;
    private ?int $categoryId = null;
    private ?int $brandId = null;
    private ?int $unitId = null;
    private Price $costPrice;
    private Price $sellingPrice;
    private ?Price $wholesalePrice = null;
    private ?Price $discountPrice = null;
    private float $taxRate = 0.0;
    private StockQuantity $currentStock;
    private StockQuantity $minStock;
    private ?StockQuantity $maxStock = null;
    private bool $trackStock = true;
    private bool $allowBackorders = false;
    private ?Weight $weight = null;
    private ?Dimensions $dimensions = null;
    private ?string $barcode = null;
    private ?string $model = null;
    private ?string $manufacturerPartNumber = null;
    private ?string $imageUrl = null;
    private ?array $additionalImages = null;
    private ?array $specifications = null;
    private bool $isActive = true;
    private bool $isFeatured = false;
    private bool $isVirtual = false;
    private bool $requiresShipping = true;
    private int $sortOrder = 0;
    private ?\DateTimeInterface $availableFrom = null;
    private ?\DateTimeInterface $availableTo = null;
    private ?array $metadata = null;
    private ?string $notes = null;

    public function __construct(string $name, string $sku, float $sellingPrice, float $costPrice)
    {
        $this->name = new ProductName($name);
        $this->sku = new Sku($sku);
        $this->sellingPrice = new Price($sellingPrice);
        $this->costPrice = new Price($costPrice);
        $this->slug = ProductSlug::fromName($name);
        $this->currentStock = new StockQuantity(0);
        $this->minStock = new StockQuantity(0);
    }

    public function withId(string $id): self
    {
        $this->id = new ProductId($id);
        return $this;
    }

    public function withDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function withShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    public function withCategoryId(?int $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function withBrandId(?int $brandId): self
    {
        $this->brandId = $brandId;
        return $this;
    }

    public function withUnitId(?int $unitId): self
    {
        $this->unitId = $unitId;
        return $this;
    }

    public function withWholesalePrice(?float $wholesalePrice): self
    {
        $this->wholesalePrice = $wholesalePrice ? new Price($wholesalePrice) : null;
        return $this;
    }

    public function withDiscountPrice(?float $discountPrice): self
    {
        $this->discountPrice = $discountPrice ? new Price($discountPrice) : null;
        return $this;
    }

    public function withTaxRate(float $taxRate): self
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    public function withCurrentStock(int $currentStock): self
    {
        $this->currentStock = new StockQuantity($currentStock);
        return $this;
    }

    public function withMinStock(int $minStock): self
    {
        $this->minStock = new StockQuantity($minStock);
        return $this;
    }

    public function withMaxStock(?int $maxStock): self
    {
        $this->maxStock = $maxStock ? new StockQuantity($maxStock) : null;
        return $this;
    }

    public function withTrackStock(bool $trackStock): self
    {
        $this->trackStock = $trackStock;
        return $this;
    }

    public function withAllowBackorders(bool $allowBackorders): self
    {
        $this->allowBackorders = $allowBackorders;
        return $this;
    }

    public function withWeight(?float $weight): self
    {
        $this->weight = $weight ? new Weight($weight) : null;
        return $this;
    }

    public function withDimensions(?float $width, ?float $height, ?float $depth): self
    {
        $this->dimensions = ($width && $height && $depth) ? new Dimensions($width, $height, $depth) : null;
        return $this;
    }

    public function withBarcode(?string $barcode): self
    {
        $this->barcode = $barcode;
        return $this;
    }

    public function withModel(?string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function withManufacturerPartNumber(?string $manufacturerPartNumber): self
    {
        $this->manufacturerPartNumber = $manufacturerPartNumber;
        return $this;
    }

    public function withImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function withAdditionalImages(?array $additionalImages): self
    {
        $this->additionalImages = $additionalImages;
        return $this;
    }

    public function withSpecifications(?array $specifications): self
    {
        $this->specifications = $specifications;
        return $this;
    }

    public function withIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function withIsFeatured(bool $isFeatured): self
    {
        $this->isFeatured = $isFeatured;
        return $this;
    }

    public function withIsVirtual(bool $isVirtual): self
    {
        $this->isVirtual = $isVirtual;
        return $this;
    }

    public function withRequiresShipping(bool $requiresShipping): self
    {
        $this->requiresShipping = $requiresShipping;
        return $this;
    }

    public function withSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function withAvailableFrom(?\DateTimeInterface $availableFrom): self
    {
        $this->availableFrom = $availableFrom;
        return $this;
    }

    public function withAvailableTo(?\DateTimeInterface $availableTo): self
    {
        $this->availableTo = $availableTo;
        return $this;
    }

    public function withMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function withNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function build(): Product
    {
        $now = new \DateTime();

        return new Product(
            $this->id,
            $this->sku,
            $this->name,
            $this->slug,
            $this->description,
            $this->shortDescription,
            $this->categoryId,
            $this->brandId,
            $this->unitId,
            $this->costPrice,
            $this->sellingPrice,
            $this->wholesalePrice,
            $this->discountPrice,
            $this->taxRate,
            $this->currentStock,
            $this->minStock,
            $this->maxStock,
            $this->trackStock,
            $this->allowBackorders,
            $this->weight,
            $this->dimensions,
            $this->barcode,
            $this->model,
            $this->manufacturerPartNumber,
            $this->imageUrl,
            $this->additionalImages,
            $this->specifications,
            $this->isActive,
            $this->isFeatured,
            $this->isVirtual,
            $this->requiresShipping,
            $this->sortOrder,
            $this->availableFrom,
            $this->availableTo,
            $this->metadata,
            $this->notes,
            $now,
            $now
        );
    }
}