<?php

namespace Src\InventoryManagement\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\InventoryManagement\Domain\Repositories\BrandRepositoryInterface;

class BrandController extends Controller
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository
    ) {}

    public function index(): JsonResponse
    {
        $brands = $this->brandRepository->findAll();
        
        return response()->json([
            'data' => $brands->toArray()
        ]);
    }

    public function show(string $id): JsonResponse
    {
        // TODO: Implementar cuando exista el método findById en el repositorio
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function store(Request $request): JsonResponse
    {
        // TODO: Implementar creación de marca
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        // TODO: Implementar actualización de marca
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(string $id): JsonResponse
    {
        // TODO: Implementar eliminación de marca
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function products(string $id): JsonResponse
    {
        // TODO: Implementar cuando exista el método findProductsByBrand en el repositorio
        return response()->json(['message' => 'Not implemented'], 501);
    }
}