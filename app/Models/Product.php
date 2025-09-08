<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_products';

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'description',
        'short_description',
        'category_id',
        'brand_id',
        'unit_id',
        'cost_price',
        'selling_price',
        'wholesale_price',
        'discount_price',
        'tax_rate',
        'current_stock',
        'min_stock',
        'max_stock',
        'track_stock',
        'allow_backorders',
        'weight',
        'length',
        'width',
        'height',
        'barcode',
        'model',
        'manufacturer_part_number',
        'image_url',
        'additional_images',
        'specifications',
        'is_active',
        'is_featured',
        'is_virtual',
        'requires_shipping',
        'sort_order',
        'available_from',
        'available_to',
        'metadata',
        'notes'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'current_stock' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'track_stock' => 'boolean',
        'allow_backorders' => 'boolean',
        'weight' => 'decimal:3',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'additional_images' => 'array',
        'specifications' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_virtual' => 'boolean',
        'requires_shipping' => 'boolean',
        'sort_order' => 'integer',
        'available_from' => 'datetime',
        'available_to' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Relación con categoría
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación con marca
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Relación con unidad
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Relación con stocks
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Scope para productos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para productos destacados
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope para productos con stock disponible
     */
    public function scopeInStock($query)
    {
        return $query->where('current_stock', '>', 0);
    }

    /**
     * Scope para productos sin stock
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    /**
     * Scope por categoría
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope por marca
     */
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Obtener el precio final (considerando descuentos)
     */
    public function getFinalPrice(): float
    {
        if (!is_null($this->discount_price) && $this->discount_price > 0) {
            return $this->discount_price;
        }

        return $this->selling_price;
    }

    /**
     * Calcular el precio con impuestos
     */
    public function getPriceWithTax(): float
    {
        $price = $this->getFinalPrice();
        return $price + ($price * $this->tax_rate / 100);
    }

    /**
     * Calcular el margen de ganancia
     */
    public function getProfitMargin(): float
    {
        if ($this->cost_price == 0) {
            return 0;
        }

        $profit = $this->getFinalPrice() - $this->cost_price;
        return ($profit / $this->cost_price) * 100;
    }

    /**
     * Verificar si está disponible (fechas de disponibilidad)
     */
    public function isAvailable(): bool
    {
        $now = now();

        if ($this->available_from && $this->available_from->gt($now)) {
            return false;
        }

        if ($this->available_to && $this->available_to->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * Verificar si tiene stock disponible
     */
    public function hasStock(): bool
    {
        if (!$this->track_stock) {
            return true;
        }

        return $this->current_stock > 0;
    }

    /**
     * Verificar si permite backorders
     */
    public function allowsBackorders(): bool
    {
        return $this->allow_backorders;
    }

    /**
     * Verificar si está por debajo del stock mínimo
     */
    public function isBelowMinimumStock(): bool
    {
        if (!$this->track_stock) {
            return false;
        }

        return $this->current_stock < $this->min_stock;
    }

    /**
     * Obtener la URL de la imagen principal o una por defecto
     */
    public function getImageUrl(): string
    {
        return $this->image_url ?? asset('images/default-product-image.png');
    }

    /**
     * Obtener todas las URLs de imágenes (principal + adicionales)
     */
    public function getAllImageUrls(): array
    {
        $images = [$this->getImageUrl()];

        if (!empty($this->additional_images)) {
            $images = array_merge($images, $this->additional_images);
        }

        return $images;
    }

    /**
     * Obtener el volumen del producto (para envíos)
     */
    public function getVolume(): float
    {
        if (is_null($this->length) || is_null($this->width) || is_null($this->height)) {
            return 0;
        }

        return $this->length * $this->width * $this->height;
    }

    /**
     * Obtener el stock total distribuido en todos los almacenes
     */
    public function getTotalDistributedStock(): int
    {
        return $this->stocks()->sum('quantity');
    }

    /**
     * Actualizar el stock actual desde los stocks distribuidos
     */
    public function updateCurrentStockFromDistributed(): void
    {
        if ($this->track_stock) {
            $this->current_stock = $this->getTotalDistributedStock();
            $this->save();
        }
    }
}