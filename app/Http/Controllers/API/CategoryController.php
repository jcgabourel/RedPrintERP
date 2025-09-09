<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $categories = Category::with(['parent', 'children'])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories: ' . $e->getMessage()
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
                'name' => 'required|string|max:100|unique:inventory_categories,name',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:inventory_categories,id',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0',
                'color' => 'nullable|string|size:7|starts_with:#',
                'icon' => 'nullable|string|max:50'
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

            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'data' => $category->load(['parent', 'children']),
                'message' => 'Category created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $category = Category::with(['parent', 'children', 'products'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:100|unique:inventory_categories,name,' . $id,
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:inventory_categories,id',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0',
                'color' => 'nullable|string|size:7|starts_with:#',
                'icon' => 'nullable|string|max:50'
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
            if (isset($validated['name']) && $validated['name'] !== $category->name) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            $category->update($validated);

            return response()->json([
                'success' => true,
                'data' => $category->fresh(['parent', 'children']),
                'message' => 'Category updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            // Check if category has products
            if ($category->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with associated products'
                ], 422);
            }

            // Check if category has children
            if ($category->children()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with subcategories'
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get only root categories (without parent)
     */
    public function rootCategories(): JsonResponse
    {
        try {
            $categories = Category::with('children')
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Root categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve root categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories with product count
     */
    public function withProductCount(): JsonResponse
    {
        try {
            $categories = Category::withCount('products')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories with product count retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories with product count: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active categories only
     */
    public function active(): JsonResponse
    {
        try {
            $categories = Category::with(['parent', 'children'])
                ->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Active categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active categories: ' . $e->getMessage()
            ], 500);
        }
    }
}
