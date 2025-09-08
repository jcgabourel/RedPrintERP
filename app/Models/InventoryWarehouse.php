<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryWarehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_warehouses';

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'contact_person',
        'contact_phone',
        'contact_email',
        'length',
        'width',
        'height',
        'dimension_unit',
        'capacity',
        'current_stock',
        'is_active',
        'is_default'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'capacity' => 'integer',
        'current_stock' => 'integer'
    ];

    /**
     * Relación con stocks
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'warehouse_id');
    }
}