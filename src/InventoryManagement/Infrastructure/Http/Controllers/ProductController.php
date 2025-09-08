<?php

namespace Src\InventoryManagement\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\InventoryManagement\Application\Commands\CreateProductCommand;
use Src\InventoryManagement\Application\Commands\UpdateProductCommand;
use Src\InventoryManagement\Application\Queries\FindProductByIdQuery;
use Src\InventoryManagement\Application\Queries\GetAllProductsQuery;
use Src\InventoryManagement\Application\Queries\GetProductStatsQuery;
use Src\InventoryManagement\Domain\Entities\Product;
use Src\InventoryManagement\Domain\Repositories\ProductRepositoryInterface;
use Src\InventoryManagement\Domain\ValueObjects\ProductId;

class ProductController extends Controller
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private GetAllProductsQuery $getAllProductsQuery,
        private FindProductByIdQuery $findProductByIdQuery,
        private GetProductStatsQuery $getProductStatsQuery
    ) {}

    public function index(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $brandId = $request->get('brand_id');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        $products = $this->getAllProductsQuery->execute(
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

        return response()->json([
            'data' => array_map(fn($product) => $product->toArray(), $products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $product = $this->findProductByIdQuery->execute(new ProductId($id));
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['data' => $product->toArray()]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'required|exists:units,id',
            'weight' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:0|gt:min_stock',
        ]);

        $command = new CreateProductCommand(
            $validated['name'],
            $validated['description'] ?? '',
            $validated['sku'],
            (float) $validated['price'],
            (float) $validated['cost'],
            $validated['category_id'],
            $validated['brand_id'] ?? null,
            $validated['unit_id'],
            $validated['weight'] ?? 0,
            $validated['width'] ?? 0,
            $validated['height'] ?? 0,
            $validated['depth'] ?? 0,
            (int) $validated['min_stock'],
            (int) $validated['max_stock']
        );

        $product = $command->execute();

        return response()->json(['data' => $product], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $product = $this->findProductByIdQuery->execute(new ProductId($id));
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $id,
            'price' => 'sometimes|required|numeric|min:0',
            'cost' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'sometimes|required|exists:units,id',
            'weight' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'min_stock' => 'sometimes|required|integer|min:0',
            'max_stock' => 'sometimes|required|integer|min:0|gt:min_stock',
        ]);

        $command = new UpdateProductCommand(
            $id,
            $validated['name'] ?? $product->getName()->getValue(),
            $validated['description'] ?? $product->getDescription(),
            $validated['sku'] ?? $product->getSku()->getValue(),
            (float) ($validated['price'] ?? $product->getSellingPrice()->getValue()),
            (float) ($validated['cost'] ?? $product->getCostPrice()->getValue()),
            $validated['category_id'] ?? $product->getCategoryId(),
            $validated['brand_id'] ?? $product->getBrandId(),
            $validated['unit_id'] ?? $product->getUnitId(),
            $validated['weight'] ?? ($product->getWeight()?->getValue() ?? 0),
            $validated['width'] ?? ($product->getDimensions()?->getWidth() ?? 0),
            $validated['height'] ?? ($product->getDimensions()?->getHeight() ?? 0),
            $validated['depth'] ?? ($product->getDimensions()?->getLength() ?? 0),
            (int) ($validated['min_stock'] ?? $product->getMinStock()->getValue()),
            (int) ($validated['max_stock'] ?? ($product->getMaxStock()?->getValue() ?? 0))
        );

        $updatedProduct = $command->execute();

        return response()->json(['data' => $updatedProduct]);
    }

    public function destroy(string $id): JsonResponse
    {
        $product = $this->findProductByIdQuery->execute(new ProductId($id));
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $this->productRepository->delete(new ProductId($id));

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function stats(): JsonResponse
    {
        $stats = $this->getProductStatsQuery->execute();

        return response()->json(['data' => $stats]);
    }

    public function stockHistory(string $id): JsonResponse
    {
        $product = $this->findProductByIdQuery->execute(new ProductId($id));
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // TODO: Implementar historial de stock cuando exista el repositorio de stock
        return response()->json(['data' => []]);
    }

    public function dashboard(): JsonResponse
    {
        $stats = $this->getProductStatsQuery->execute();
        
        // Usar métodos existentes del repositorio
        $lowStockProducts = $this->productRepository->findLowStock();
        $outOfStockProducts = $this->productRepository->findOutOfStock();

        return response()->json([
            'stats' => $stats,
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'recent_movements' => [] // TODO: Implementar cuando exista repositorio de movimientos
        ]);
    }
}