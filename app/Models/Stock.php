<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_stocks';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'reserved_quantity',
        'incoming_quantity',
        'minimum_stock',
        'maximum_stock',
        'location',
        'bin',
        'rack',
        'section',
        'unit_cost',
        'total_value',
        'last_received_at',
        'last_sold_at',
        'last_counted_at',
        'is_active',
        'is_primary',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'incoming_quantity' => 'integer',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_value' => 'decimal:2',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'last_received_at' => 'datetime',
        'last_sold_at' => 'datetime',
        'last_counted_at' => 'datetime'
    ];

    /**
     * Relación con producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación con almacén
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Scope para stocks activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ubicaciones primarias
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope por producto
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope por almacén
     */
    public function scopeForWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    /**
     * Obtener stock disponible (quantity - reserved)
     */
    public function getAvailableQuantity(): int
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    /**
     * Obtener stock total (available + incoming)
     */
    public function getTotalQuantity(): int
    {
        return $this->getAvailableQuantity() + $this->incoming_quantity;
    }

    /**
     * Verificar si está por debajo del stock mínimo
     */
    public function isBelowMinimum(): bool
    {
        return $this->getAvailableQuantity() < $this->minimum_stock;
    }

    /**
     * Verificar si está por encima del stock máximo
     */
    public function isAboveMaximum(): bool
    {
        if (is_null($this->maximum_stock)) {
            return false;
        }
        
        return $this->getAvailableQuantity() > $this->maximum_stock;
    }

    /**
     * Obtener la ubicación completa formateada
     */
    public function getFullLocation(): string
    {
        $parts = [];

        if (!empty($this->location)) {
            $parts[] = $this->location;
        }

        if (!empty($this->rack)) {
            $parts[] = "Rack: {$this->rack}";
        }

        if (!empty($this->section)) {
            $parts[] = "Sección: {$this->section}";
        }

        if (!empty($this->bin)) {
            $parts[] = "Bin: {$this->bin}";
        }

        return implode(' - ', $parts);
    }

    /**
     * Actualizar el valor total automáticamente
     */
    public function updateTotalValue(): void
    {
        $this->total_value = $this->quantity * ($this->unit_cost ?? 0);
        $this->save();
    }

    /**
     * Verificar si es la ubicación primaria
     */
    public function isPrimary(): bool
    {
        return $this->is_primary;
    }
}