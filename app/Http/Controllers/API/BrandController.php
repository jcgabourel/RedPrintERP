<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $brands = Brand::withCount('products')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $brands,
                'message' => 'Brands retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brands: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:inventory_brands,name',
                'description' => 'nullable|string',
                'website' => 'nullable|url',
                'contact_email' => 'nullable|email',
                'contact_phone' => 'nullable|string|max:20',
                'logo_url' => 'nullable|url',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0',
                'metadata' => 'nullable|json'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $validated['slug'] = Str::slug($validated['name']);

            // Parse metadata if provided
            if (isset($validated['metadata'])) {
                $validated['metadata'] = json_decode($validated['metadata'], true);
            }

            $brand = Brand::create($validated);

            return response()->json([
                'success' => true,
                'data' => $brand,
                'message' => 'Brand created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create brand: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $brand = Brand::with(['products'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $brand,
                'message' => 'Brand retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $brand = Brand::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:100|unique:inventory_brands,name,' . $id,
                'description' => 'nullable|string',
                'website' => 'nullable|url',
                'contact_email' => 'nullable|email',
                'contact_phone' => 'nullable|string|max:20',
                'logo_url' => 'nullable|url',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0',
                'metadata' => 'nullable|json'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Update slug if name changed
            if (isset($validated['name']) && $validated['name'] !== $brand->name) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Parse metadata if provided
            if (isset($validated['metadata'])) {
                $validated['metadata'] = json_decode($validated['metadata'], true);
            }

            $brand->update($validated);

            return response()->json([
                'success' => true,
                'data' => $brand->fresh(),
                'message' => 'Brand updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update brand: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $brand = Brand::findOrFail($id);

            // Check if brand has products
            if ($brand->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete brand with associated products'
                ], 422);
            }

            $brand->delete();

            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete brand: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active brands only
     */
    public function active(): JsonResponse
    {
        try {
            $brands = Brand::withCount('products')
                ->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $brands,
                'message' => 'Active brands retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active brands: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get brands with contact information
     */
    public function withContactInfo(): JsonResponse
    {
        try {
            $brands = Brand::withCount('products')
                ->where(function ($query) {
                    $query->whereNotNull('contact_email')
                          ->orWhereNotNull('contact_phone');
                })
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $brands,
                'message' => 'Brands with contact information retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brands with contact information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search brands by name
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'q' => 'required|string|min:2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $searchTerm = $request->input('q');

            $brands = Brand::where('name', 'like', "%{$searchTerm}%")
                ->orWhere('description', 'like', "%{$searchTerm}%")
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $brands,
                'message' => 'Brands search results retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search brands: ' . $e->getMessage()
            ], 500);
        }
    }
}
