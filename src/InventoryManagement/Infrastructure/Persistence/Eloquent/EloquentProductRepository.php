<?php

namespace Src\InventoryManagement\Infrastructure\Persistence\Eloquent;

use Src\InventoryManagement\Domain\Entities\Product;
use Src\InventoryManagement\Domain\Repositories\ProductRepositoryInterface;
use Src\InventoryManagement\Domain\ValueObjects\ProductId;
use Src\InventoryManagement\Domain\ValueObjects\CategoryId;
use Src\InventoryManagement\Domain\ValueObjects\BrandId;
use Src\InventoryManagement\Domain\ValueObjects\Sku;
use Src\InventoryManagement\Domain\ValueObjects\ProductName;
use Src\InventoryManagement\Domain\ValueObjects\ProductSlug;
use Src\InventoryManagement\Domain\ValueObjects\Price;
use Src\InventoryManagement\Domain\ValueObjects\StockQuantity;
use Src\InventoryManagement\Domain\ValueObjects\Weight;
use Src\InventoryManagement\Domain\ValueObjects\Dimensions;
use App\Models\Product as ProductModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findById(ProductId $id): ?Product
    {
        $model = ProductModel::with(['category', 'brand', 'unit'])->find($id->getValue());

        if (!$model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function findBySku(Sku $sku): ?Product
    {
        $model = ProductModel::with(['category', 'brand', 'unit'])
            ->where('sku', $sku->getValue())
            ->first();

        return $model ? $this->mapToEntity($model) : null;
    }

    public function save(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $data = $this->mapToModelData($product);

            if ($product->getId()?->isNull() ?? true) {
                // Crear nuevo producto
                $model = new ProductModel();
                $model->fill($data);
                $model->save();

                // Actualizar la entidad con el ID generado
                $reflection = new \ReflectionClass($product);
                $property = $reflection->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($product, new ProductId($model->id));
            } else {
                // Actualizar producto existente
                $model = ProductModel::findOrFail($product->getId()->getValue());
                $model->fill($data);
                $model->save();
            }

            // Actualizar contadores en categoría y marca
            $this->updateCategoryProductCount($model->category_id);
            $this->updateBrandProductCount($model->brand_id);
        });
    }

    public function delete(ProductId $id): bool
    {
        return DB::transaction(function () use ($id) {
            $model = ProductModel::findOrFail($id->getValue());

            // Verificar si tiene stock asociado
            if ($model->stocks()->count() > 0) {
                throw new InvalidArgumentException('Cannot delete product with associated stock');
            }

            $categoryId = $model->category_id;
            $brandId = $model->brand_id;

            $deleted = $model->delete();

            if ($deleted) {
                $this->updateCategoryProductCount($categoryId);
                $this->updateBrandProductCount($brandId);
            }

            return $deleted;
        });
    }

    public function findAll(): Collection
    {
        return ProductModel::with(['category', 'brand', 'unit'])
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findActive(): Collection
    {
        return ProductModel::with(['category', 'brand', 'unit'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByCategoryId(CategoryId $categoryId): Collection
    {
        return ProductModel::with(['category', 'brand', 'unit'])
            ->where('category_id', $categoryId->getValue())
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByBrandId(BrandId $brandId): Collection
    {
        return ProductModel::with(['category', 'brand', 'unit'])
            ->where('brand_id', $brandId->getValue())
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function searchByName(string $name): Collection
    {
        return ProductModel::with(['category', 'brand', 'unit'])
            ->where('name', 'like', "%{$name}%")
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function searchByDescription(string $description): Collection
    {
        return ProductModel::with(['category', 'brand', 'unit'])
            ->where('description', 'like', "%{$description}%")
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return ProductModel::with(['category', 'brand', 'unit'])
            ->whereBetween('selling_price', [$minPrice, $maxPrice])
            ->where('is_active', true)
            ->orderBy('selling_price')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findLowStock(int $threshold = 10): Collection
    {
        return ProductModel::with(['category', 'brand', 'unit'])
            ->whereHas('stocks', function ($query) use ($threshold) {
                $query->where('quantity', '<=', $threshold)
                      ->where('quantity', '>', 0);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function findOutOfStock(): Collection
    {
        return ProductModel::with(['category', 'brand', 'unit'])
            ->whereDoesntHave('stocks', function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function getProductsWithTotalStock(): Collection
    {
        return ProductModel::with(['category', 'brand', 'unit'])
            ->leftJoin('inventory_stocks', 'inventory_products.id', '=', 'inventory_stocks.product_id')
            ->selectRaw('inventory_products.*, COALESCE(SUM(inventory_stocks.quantity), 0) as total_stock')
            ->where('inventory_products.is_active', true)
            ->groupBy('inventory_products.id')
            ->orderBy('total_stock', 'desc')
            ->get()
            ->map(fn($model) => $this->mapToEntity($model));
    }

    public function exists(ProductId $id): bool
    {
        return ProductModel::where('id', $id->getValue())->exists();
    }

    public function existsWithSku(Sku $sku, ?ProductId $excludeId = null): bool
    {
        $query = ProductModel::where('sku', $sku->getValue());

        if ($excludeId && !$excludeId->isNull()) {
            $query->where('id', '!=', $excludeId->getValue());
        }

        return $query->exists();
    }

    public function getTotalInventoryValue(): float
    {
        return ProductModel::join('inventory_stocks', 'inventory_products.id', '=', 'inventory_stocks.product_id')
            ->where('inventory_products.is_active', true)
            ->sum(DB::raw('inventory_stocks.quantity * inventory_stocks.unit_cost'));
    }

    public function updateProductStockCount(ProductId $productId): void
    {
        // Los contadores de stock se manejan a nivel de repositorio de stock
        // Este método podría ser útil para otras métricas específicas del producto
    }

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
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
        $query = ProductModel::with(['category', 'brand', 'unit'])
            ->where('is_active', true);

        // Aplicar filtros
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        if ($minPrice !== null) {
            $query->where('selling_price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('selling_price', '<=', $maxPrice);
        }

        // Aplicar ordenamiento
        $validSortFields = ['name', 'selling_price', 'created_at', 'updated_at'];
        $sortField = in_array($sortBy, $validSortFields) ? $sortBy : 'name';
        $sortDirection = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';
        
        $query->orderBy($sortField, $sortDirection);

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);
        
        // Crear una nueva colección con las entidades mapeadas
        $items = $paginator->items();
        $mappedItems = array_map(fn($model) => $this->mapToEntity($model), $items);
        
        // Crear un nuevo LengthAwarePaginator con los items mapeados
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $mappedItems,
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
            [
                'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }

    private function updateCategoryProductCount(?int $categoryId): void
    {
        if ($categoryId) {
            $productCount = ProductModel::where('category_id', $categoryId)
                ->where('is_active', true)
                ->count();

            \App\Models\Category::where('id', $categoryId)
                ->update(['product_count' => $productCount]);
        }
    }

    private function updateBrandProductCount(?int $brandId): void
    {
        if ($brandId) {
            $productCount = ProductModel::where('brand_id', $brandId)
                ->where('is_active', true)
                ->count();

            \App\Models\Brand::where('id', $brandId)
                ->update(['product_count' => $productCount]);
        }
    }

    private function mapToEntity(ProductModel $model): Product
    {
        $weight = null;
        if ($model->weight && $model->weight_unit) {
            $weight = new Weight((float) $model->weight, $model->weight_unit);
        }

        $dimensions = null;
        if ($model->length && $model->width && $model->height) {
            $dimensions = new Dimensions(
                (float) $model->length,
                (float) $model->width,
                (float) $model->height,
                $model->dimension_unit
            );
        }

        return new Product(
            $model->id ? new ProductId($model->id) : null,
            new Sku($model->sku),
            new ProductName($model->name),
            new ProductSlug($model->slug),
            $model->description,
            $model->short_description,
            $model->category_id,
            $model->brand_id,
            $model->unit_id,
            new Price($model->cost_price),
            new Price($model->selling_price),
            $model->wholesale_price ? new Price($model->wholesale_price) : null,
            $model->discount_price ? new Price($model->discount_price) : null,
            (float) $model->tax_rate,
            new StockQuantity($model->current_stock),
            new StockQuantity($model->min_stock),
            $model->max_stock ? new StockQuantity($model->max_stock) : null,
            (bool) $model->track_stock,
            (bool) $model->allow_backorders,
            $weight,
            $dimensions,
            $model->barcode,
            $model->model,
            $model->manufacturer_part_number,
            $model->image_url,
            $model->additional_images ? json_decode($model->additional_images, true) : null,
            $model->specifications ? json_decode($model->specifications, true) : null,
            (bool) $model->is_active,
            (bool) $model->is_featured,
            (bool) $model->is_virtual,
            (bool) $model->requires_shipping,
            (int) $model->sort_order,
            $model->available_from ? new \DateTime($model->available_from) : null,
            $model->available_to ? new \DateTime($model->available_to) : null,
            $model->metadata ? json_decode($model->metadata, true) : null,
            $model->notes,
            new \DateTime($model->created_at),
            new \DateTime($model->updated_at),
            $model->deleted_at ? new \DateTime($model->deleted_at) : null
        );
    }

    private function mapToModelData(Product $entity): array
    {
        return [
            'id' => $entity->getId()?->getValue(),
            'sku' => $entity->getSku()->getValue(),
            'name' => $entity->getName()->getValue(),
            'slug' => $entity->getSlug(),
            'description' => $entity->getDescription(),
            'short_description' => $entity->getShortDescription(),
            'category_id' => $entity->getCategoryId(),
            'brand_id' => $entity->getBrandId(),
            'unit_id' => $entity->getUnitId(),
            'cost_price' => $entity->getCostPrice()->getValue(),
            'selling_price' => $entity->getSellingPrice()->getValue(),
            'wholesale_price' => $entity->getWholesalePrice()?->getValue(),
            'discount_price' => $entity->getDiscountPrice()?->getValue(),
            'tax_rate' => $entity->getTaxRate(),
            'current_stock' => $entity->getCurrentStock()->getValue(),
            'min_stock' => $entity->getMinStock()->getValue(),
            'max_stock' => $entity->getMaxStock()?->getValue(),
            'track_stock' => $entity->shouldTrackStock(),
            'allow_backorders' => $entity->allowsBackorders(),
            'weight' => $entity->getWeight()?->getValue(),
            'weight_unit' => $entity->getWeight()?->getUnit(),
            'length' => $entity->getDimensions()?->getLength(),
            'width' => $entity->getDimensions()?->getWidth(),
            'height' => $entity->getDimensions()?->getHeight(),
            'dimension_unit' => $entity->getDimensions()?->getUnit(),
            'barcode' => $entity->getBarcode(),
            'model' => $entity->getModel(),
            'manufacturer_part_number' => $entity->getManufacturerPartNumber(),
            'image_url' => $entity->getImageUrl(),
            'additional_images' => $entity->getAdditionalImages() ? json_encode($entity->getAdditionalImages()) : null,
            'specifications' => $entity->getSpecifications() ? json_encode($entity->getSpecifications()) : null,
            'is_active' => $entity->isActive(),
            'is_featured' => $entity->isFeatured(),
            'is_virtual' => $entity->isVirtual(),
            'requires_shipping' => $entity->requiresShipping(),
            'sort_order' => $entity->getSortOrder(),
            'available_from' => $entity->getAvailableFrom()?->format('Y-m-d H:i:s'),
            'available_to' => $entity->getAvailableTo()?->format('Y-m-d H:i:s'),
            'metadata' => $entity->getMetadata() ? json_encode($entity->getMetadata()) : null,
            'notes' => $entity->getNotes(),
            'created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()->format('Y-m-d H:i:s'),
            'deleted_at' => $entity->getDeletedAt()?->format('Y-m-d H:i:s')
        ];
    }
}