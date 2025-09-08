<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_warehouses';

    protected $fillable = [
        'name',
        'code',
        'type',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'contact_name',
        'contact_phone',
        'contact_email',
        'latitude',
        'longitude',
        'area_square_meters',
        'storage_capacity',
        'is_active',
        'is_default',
        'operating_hours',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'area_square_meters' => 'decimal:2',
        'storage_capacity' => 'integer',
        'operating_hours' => 'array'
    ];

    /**
     * Relación con stocks
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Scope para almacenes activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para almacenes por defecto
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope por tipo de almacén
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Obtener la dirección completa formateada
     */
    public function getFullAddress(): string
    {
        $parts = [];

        if (!empty($this->address)) {
            $parts[] = $this->address;
        }

        if (!empty($this->city)) {
            $parts[] = $this->city;
        }

        if (!empty($this->state)) {
            $parts[] = $this->state;
        }

        if (!empty($this->zip_code)) {
            $parts[] = $this->zip_code;
        }

        if (!empty($this->country)) {
            $parts[] = $this->country;
        }

        return implode(', ', $parts);
    }

    /**
     * Verificar si tiene coordenadas geográficas
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Obtener información de contacto formateada
     */
    public function getContactInfo(): string
    {
        $contact = [];

        if (!empty($this->contact_name)) {
            $contact[] = $this->contact_name;
        }

        if (!empty($this->contact_phone)) {
            $contact[] = "Tel: {$this->contact_phone}";
        }

        if (!empty($this->contact_email)) {
            $contact[] = "Email: {$this->contact_email}";
        }

        return implode(' | ', $contact);
    }

    /**
     * Obtener el porcentaje de capacidad utilizado
     */
    public function getCapacityUsagePercentage(): float
    {
        if (!$this->storage_capacity) {
            return 0;
        }

        $totalStock = $this->stocks()->sum('quantity');
        return ($totalStock / $this->storage_capacity) * 100;
    }

    /**
     * Verificar si es el almacén por defecto
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }
}