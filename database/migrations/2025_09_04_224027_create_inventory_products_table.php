<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_products', function (Blueprint $table) {
            $table->id();
            
            // Información básica del producto
            $table->string('sku', 50)->unique()->comment('SKU único del producto');
            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            
            // Relaciones con otras tablas
            $table->foreignId('category_id')->nullable()->constrained('inventory_categories')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained('inventory_brands')->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained('inventory_units')->onDelete('set null');
            
            // Información de precios y costos
            $table->decimal('cost_price', 15, 2)->default(0)->comment('Costo de adquisición');
            $table->decimal('selling_price', 15, 2)->default(0)->comment('Precio de venta');
            $table->decimal('wholesale_price', 15, 2)->nullable()->comment('Precio por mayoreo');
            $table->decimal('discount_price', 15, 2)->nullable()->comment('Precio con descuento');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('Porcentaje de impuesto');
            
            // Control de inventario
            $table->integer('current_stock')->default(0)->comment('Stock actual');
            $table->integer('min_stock')->default(0)->comment('Stock mínimo alerta');
            $table->integer('max_stock')->nullable()->comment('Stock máximo');
            $table->boolean('track_stock')->default(true)->comment('Si se lleva control de stock');
            $table->boolean('allow_backorders')->default(false)->comment('Permitir ventas sin stock');
            
            // Información física
            $table->decimal('weight', 10, 3)->nullable()->comment('Peso en kg');
            $table->decimal('length', 10, 2)->nullable()->comment('Longitud en cm');
            $table->decimal('width', 10, 2)->nullable()->comment('Ancho en cm');
            $table->decimal('height', 10, 2)->nullable()->comment('Alto en cm');
            
            // Información adicional
            $table->string('barcode', 100)->nullable()->comment('Código de barras');
            $table->string('model', 100)->nullable()->comment('Modelo del producto');
            $table->string('manufacturer_part_number', 100)->nullable()->comment('Número de parte del fabricante');
            $table->string('image_url')->nullable()->comment('URL de la imagen principal');
            $table->json('additional_images')->nullable()->comment('URLs de imágenes adicionales en JSON');
            $table->json('specifications')->nullable()->comment('Especificaciones técnicas en JSON');
            
            // Estado y visibilidad
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_virtual')->default(false)->comment('Producto digital/servicio');
            $table->boolean('requires_shipping')->default(true);
            $table->integer('sort_order')->default(0);
            
            // Fechas importantes
            $table->timestamp('available_from')->nullable()->comment('Disponible a partir de');
            $table->timestamp('available_to')->nullable()->comment('Disponible hasta');
            
            // Metadata
            $table->json('metadata')->nullable()->comment('Información adicional en JSON');
            $table->text('notes')->nullable()->comment('Notas internas');
            
            // Índices
            $table->index('sku');
            $table->index('name');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('unit_id');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('current_stock');
            $table->index('track_stock');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_products');
    }
};
