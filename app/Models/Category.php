<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'parent_id',
        'sort_order',
        'color',
        'icon'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Relación con categorías padre
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relación con categorías hijas
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Relación con productos
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope para categorías activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para categorías padre (sin parent_id)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Obtener la ruta completa de la categoría (para breadcrumbs)
     */
    public function getFullPath(): array
    {
        $path = [];
        $current = $this;

        while ($current) {
            $path[] = $current;
            $current = $current->parent;
        }

        return array_reverse($path);
    }

    /**
     * Verificar si la categoría tiene hijos
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Obtener el número total de productos en esta categoría (incluyendo subcategorías)
     */
    public function getTotalProductsCount(): int
    {
        $categoryIds = $this->getAllDescendantIds();
        $categoryIds[] = $this->id;

        return Product::whereIn('category_id', $categoryIds)->count();
    }

    /**
     * Obtener todos los IDs de categorías descendientes
     */
    private function getAllDescendantIds(): array
    {
        $ids = [];
        $this->getDescendantIdsRecursive($this, $ids);
        return $ids;
    }

    /**
     * Método recursivo para obtener IDs descendientes
     */
    private function getDescendantIdsRecursive(Category $category, array &$ids): void
    {
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $this->getDescendantIdsRecursive($child, $ids);
        }
    }
}