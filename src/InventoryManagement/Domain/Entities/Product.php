<?php

namespace Src\InventoryManagement\Domain\Entities;

use Src\InventoryManagement\Domain\ValueObjects\ProductId;
use Src\InventoryManagement\Domain\ValueObjects\Sku;
use Src\InventoryManagement\Domain\ValueObjects\ProductName;
use Src\InventoryManagement\Domain\ValueObjects\ProductSlug;
use Src\InventoryManagement\Domain\ValueObjects\Price;
use Src\InventoryManagement\Domain\ValueObjects\StockQuantity;
use Src\InventoryManagement\Domain\ValueObjects\Weight;
use Src\InventoryManagement\Domain\ValueObjects\Dimensions;

class Product
{
    private ?ProductId $id;
    private Sku $sku;
    private ProductName $name;
    private ProductSlug $slug;
    private ?string $description;
    private ?string $shortDescription;
    private ?int $categoryId;
    private ?int $brandId;
    private ?int $unitId;
    private Price $costPrice;
    private Price $sellingPrice;
    private ?Price $wholesalePrice;
    private ?Price $discountPrice;
    private float $taxRate;
    private StockQuantity $currentStock;
    private StockQuantity $minStock;
    private ?StockQuantity $maxStock;
    private bool $trackStock;
    private bool $allowBackorders;
    private ?Weight $weight;
    private ?Dimensions $dimensions;
    private ?string $barcode;
    private ?string $model;
    private ?string $manufacturerPartNumber;
    private ?string $imageUrl;
    private ?array $additionalImages;
    private ?array $specifications;
    private bool $isActive;
    private bool $isFeatured;
    private bool $isVirtual;
    private bool $requiresShipping;
    private int $sortOrder;
    private ?\DateTimeInterface $availableFrom;
    private ?\DateTimeInterface $availableTo;
    private ?array $metadata;
    private ?string $notes;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;
    private ?\DateTimeInterface $deletedAt;

    public function __construct(
        ?ProductId $id,
        Sku $sku,
        ProductName $name,
        ProductSlug $slug,
        ?string $description,
        ?string $shortDescription,
        ?int $categoryId,
        ?int $brandId,
        ?int $unitId,
        Price $costPrice,
        Price $sellingPrice,
        ?Price $wholesalePrice,
        ?Price $discountPrice,
        float $taxRate,
        StockQuantity $currentStock,
        StockQuantity $minStock,
        ?StockQuantity $maxStock,
        bool $trackStock,
        bool $allowBackorders,
        ?Weight $weight,
        ?Dimensions $dimensions,
        ?string $barcode,
        ?string $model,
        ?string $manufacturerPartNumber,
        ?string $imageUrl,
        ?array $additionalImages,
        ?array $specifications,
        bool $isActive,
        bool $isFeatured,
        bool $isVirtual,
        bool $requiresShipping,
        int $sortOrder,
        ?\DateTimeInterface $availableFrom,
        ?\DateTimeInterface $availableTo,
        ?array $metadata,
        ?string $notes,
        \DateTimeInterface $createdAt,
        \DateTimeInterface $updatedAt,
        ?\DateTimeInterface $deletedAt = null
    ) {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->shortDescription = $shortDescription;
        $this->categoryId = $categoryId;
        $this->brandId = $brandId;
        $this->unitId = $unitId;
        $this->costPrice = $costPrice;
        $this->sellingPrice = $sellingPrice;
        $this->wholesalePrice = $wholesalePrice;
        $this->discountPrice = $discountPrice;
        $this->taxRate = $taxRate;
        $this->currentStock = $currentStock;
        $this->minStock = $minStock;
        $this->maxStock = $maxStock;
        $this->trackStock = $trackStock;
        $this->allowBackorders = $allowBackorders;
        $this->weight = $weight;
        $this->dimensions = $dimensions;
        $this->barcode = $barcode;
        $this->model = $model;
        $this->manufacturerPartNumber = $manufacturerPartNumber;
        $this->imageUrl = $imageUrl;
        $this->additionalImages = $additionalImages;
        $this->specifications = $specifications;
        $this->isActive = $isActive;
        $this->isFeatured = $isFeatured;
        $this->isVirtual = $isVirtual;
        $this->requiresShipping = $requiresShipping;
        $this->sortOrder = $sortOrder;
        $this->availableFrom = $availableFrom;
        $this->availableTo = $availableTo;
        $this->metadata = $metadata;
        $this->notes = $notes;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deletedAt = $deletedAt;
    }

    // Getters
    public function getId(): ?ProductId
    {
        return $this->id;
    }

    public function getSku(): Sku
    {
        return $this->sku;
    }

    public function getName(): ProductName
    {
        return $this->name;
    }

    public function getSlug(): ProductSlug
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getBrandId(): ?int
    {
        return $this->brandId;
    }

    public function getUnitId(): ?int
    {
        return $this->unitId;
    }

    public function getCostPrice(): Price
    {
        return $this->costPrice;
    }

    public function getSellingPrice(): Price
    {
        return $this->sellingPrice;
    }

    public function getWholesalePrice(): ?Price
    {
        return $this->wholesalePrice;
    }

    public function getDiscountPrice(): ?Price
    {
        return $this->discountPrice;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    public function getCurrentStock(): StockQuantity
    {
        return $this->currentStock;
    }

    public function getMinStock(): StockQuantity
    {
        return $this->minStock;
    }

    public function getMaxStock(): ?StockQuantity
    {
        return $this->maxStock;
    }

    public function shouldTrackStock(): bool
    {
        return $this->trackStock;
    }

    public function allowsBackorders(): bool
    {
        return $this->allowBackorders;
    }

    public function getWeight(): ?Weight
    {
        return $this->weight;
    }

    public function getDimensions(): ?Dimensions
    {
        return $this->dimensions;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function getManufacturerPartNumber(): ?string
    {
        return $this->manufacturerPartNumber;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getAdditionalImages(): ?array
    {
        return $this->additionalImages;
    }

    public function getSpecifications(): ?array
    {
        return $this->specifications;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }

    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }

    public function requiresShipping(): bool
    {
        return $this->requiresShipping;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function getAvailableFrom(): ?\DateTimeInterface
    {
        return $this->availableFrom;
    }

    public function getAvailableTo(): ?\DateTimeInterface
    {
        return $this->availableTo;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    // Business logic methods
    public function getFinalPrice(): Price
    {
        if ($this->discountPrice !== null && $this->discountPrice->getValue() > 0) {
            return $this->discountPrice;
        }

        return $this->sellingPrice;
    }

    public function getPriceWithTax(): Price
    {
        $price = $this->getFinalPrice();
        $taxAmount = $price->getValue() * ($this->taxRate / 100);
        
        return new Price($price->getValue() + $taxAmount);
    }

    public function getProfitMargin(): float
    {
        if ($this->costPrice->getValue() == 0) {
            return 0;
        }

        $profit = $this->getFinalPrice()->getValue() - $this->costPrice->getValue();
        return ($profit / $this->costPrice->getValue()) * 100;
    }

    public function isAvailable(): bool
    {
        $now = new \DateTime();

        if ($this->availableFrom && $this->availableFrom > $now) {
            return false;
        }

        if ($this->availableTo && $this->availableTo < $now) {
            return false;
        }

        return $this->isActive;
    }

    public function hasStock(): bool
    {
        if (!$this->trackStock) {
            return true;
        }

        return $this->currentStock->getValue() > 0;
    }

    public function isBelowMinimumStock(): bool
    {
        if (!$this->trackStock) {
            return false;
        }

        return $this->currentStock->getValue() < $this->minStock->getValue();
    }

    public function isAboveMaximumStock(): bool
    {
        if (!$this->trackStock || $this->maxStock === null) {
            return false;
        }

        return $this->currentStock->getValue() > $this->maxStock->getValue();
    }

    public function updateStock(StockQuantity $newStock): void
    {
        if ($this->trackStock) {
            $this->currentStock = $newStock;
            $this->updatedAt = new \DateTime();
        }
    }

    public function incrementStock(int $quantity = 1): void
    {
        if ($this->trackStock) {
            $newValue = $this->currentStock->getValue() + $quantity;
            $this->currentStock = new StockQuantity($newValue);
            $this->updatedAt = new \DateTime();
        }
    }

    public function decrementStock(int $quantity = 1): void
    {
        if ($this->trackStock) {
            $newValue = max(0, $this->currentStock->getValue() - $quantity);
            $this->currentStock = new StockQuantity($newValue);
            $this->updatedAt = new \DateTime();
        }
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTime();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new \DateTime();
    }

    public function feature(): void
    {
        $this->isFeatured = true;
        $this->updatedAt = new \DateTime();
    }

    public function unfeature(): void
    {
        $this->isFeatured = false;
        $this->updatedAt = new \DateTime();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id?->getValue(),
            'sku' => $this->sku->getValue(),
            'name' => $this->name->getValue(),
            'slug' => $this->slug->value(),
            'description' => $this->description,
            'short_description' => $this->shortDescription,
            'category_id' => $this->categoryId,
            'brand_id' => $this->brandId,
            'unit_id' => $this->unitId,
            'cost_price' => $this->costPrice->getValue(),
            'selling_price' => $this->sellingPrice->getValue(),
            'wholesale_price' => $this->wholesalePrice?->getValue(),
            'discount_price' => $this->discountPrice?->getValue(),
            'tax_rate' => $this->taxRate,
            'current_stock' => $this->currentStock->getValue(),
            'min_stock' => $this->minStock->getValue(),
            'max_stock' => $this->maxStock?->getValue(),
            'track_stock' => $this->trackStock,
            'allow_backorders' => $this->allowBackorders,
            'weight' => $this->weight?->getValue(),
            'weight_unit' => $this->weight?->getUnit(),
            'length' => $this->dimensions?->getLength(),
            'width' => $this->dimensions?->getWidth(),
            'height' => $this->dimensions?->getHeight(),
            'dimension_unit' => $this->dimensions?->getUnit(),
            'barcode' => $this->barcode,
            'model' => $this->model,
            'manufacturer_part_number' => $this->manufacturerPartNumber,
            'image_url' => $this->imageUrl,
            'additional_images' => $this->additionalImages,
            'specifications' => $this->specifications,
            'is_active' => $this->isActive,
            'is_featured' => $this->isFeatured,
            'is_virtual' => $this->isVirtual,
            'requires_shipping' => $this->requiresShipping,
            'sort_order' => $this->sortOrder,
            'available_from' => $this->availableFrom?->format('Y-m-d H:i:s'),
            'available_to' => $this->availableTo?->format('Y-m-d H:i:s'),
            'metadata' => $this->metadata,
            'notes' => $this->notes,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }
}