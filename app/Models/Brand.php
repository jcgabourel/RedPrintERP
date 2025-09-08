<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_brands';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'contact_email',
        'contact_phone',
        'logo_url',
        'is_active',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array'
    ];

    /**
     * Relación con productos
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope para marcas activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtener el número total de productos de esta marca
     */
    public function getProductsCount(): int
    {
        return $this->products()->count();
    }

    /**
     * Obtener la URL del logo o una imagen por defecto
     */
    public function getLogoUrl(): string
    {
        return $this->logo_url ?? asset('images/default-brand-logo.png');
    }

    /**
     * Verificar si la marca tiene información de contacto
     */
    public function hasContactInfo(): bool
    {
        return !empty($this->contact_email) || !empty($this->contact_phone);
    }

    /**
     * Obtener información de contacto formateada
     */
    public function getContactInfo(): string
    {
        $contact = [];

        if (!empty($this->contact_email)) {
            $contact[] = "Email: {$this->contact_email}";
        }

        if (!empty($this->contact_phone)) {
            $contact[] = "Tel: {$this->contact_phone}";
        }

        return implode(' | ', $contact);
    }
}