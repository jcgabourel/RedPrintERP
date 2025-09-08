<?php

namespace Src\InventoryManagement\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\InventoryManagement\Domain\Repositories\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryRepository->findAll();
        
        return response()->json([
            'data' => $categories->toArray()
        ]);
    }

    public function show(string $id): JsonResponse
    {
        // TODO: Implementar cuando exista el método findById en el repositorio
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function store(Request $request): JsonResponse
    {
        // TODO: Implementar creación de categoría
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        // TODO: Implementar actualización de categoría
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(string $id): JsonResponse
    {
        // TODO: Implementar eliminación de categoría
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function products(string $id): JsonResponse
    {
        // TODO: Implementar cuando exista el método findProductsByCategory en el repositorio
        return response()->json(['message' => 'Not implemented'], 501);
    }
}