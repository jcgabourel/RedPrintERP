<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_units';

    protected $fillable = [
        'name',
        'abbreviation',
        'unit_type',
        'conversion_factor',
        'is_base_unit',
        'description',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_base_unit' => 'boolean',
        'conversion_factor' => 'decimal:6',
        'sort_order' => 'integer'
    ];

    /**
     * Relación con productos
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope para unidades activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para unidades base
     */
    public function scopeBaseUnits($query)
    {
        return $query->where('is_base_unit', true);
    }

    /**
     * Scope por tipo de unidad
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('unit_type', $type);
    }

    /**
     * Convertir cantidad a unidad base
     */
    public function convertToBase(float $quantity): float
    {
        return $quantity * $this->conversion_factor;
    }

    /**
     * Convertir cantidad desde unidad base
     */
    public function convertFromBase(float $quantity): float
    {
        if ($this->conversion_factor == 0) {
            return 0;
        }
        
        return $quantity / $this->conversion_factor;
    }

    /**
     * Verificar si es unidad base
     */
    public function isBase(): bool
    {
        return $this->is_base_unit;
    }

    /**
     * Obtener el nombre completo con abreviatura
     */
    public function getFullName(): string
    {
        return "{$this->name} ({$this->abbreviation})";
    }

    /**
     * Obtener el número total de productos usando esta unidad
     */
    public function getProductsCount(): int
    {
        return $this->products()->count();
    }
}