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
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            
            // Relaciones con producto y almacén
            $table->foreignId('product_id')->constrained('inventory_products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->onDelete('cascade');
            
            // Información de stock
            $table->integer('quantity')->default(0)->comment('Cantidad disponible');
            $table->integer('reserved_quantity')->default(0)->comment('Cantidad reservada (en órdenes)');
            $table->integer('incoming_quantity')->default(0)->comment('Cantidad en tránsito/por recibir');
            $table->integer('minimum_stock')->default(0)->comment('Stock mínimo para este almacén');
            $table->integer('maximum_stock')->nullable()->comment('Stock máximo para este almacén');
            
            // Ubicación dentro del almacén
            $table->string('location', 100)->nullable()->comment('Ubicación física (estante, pasillo, etc.)');
            $table->string('bin', 50)->nullable()->comment('Número de contenedor/bin');
            $table->string('rack', 50)->nullable()->comment('Número de rack/estante');
            $table->string('section', 50)->nullable()->comment('Sección del almacén');
            
            // Información de costos específicos por almacén
            $table->decimal('unit_cost', 15, 2)->nullable()->comment('Costo unitario específico para este almacén');
            $table->decimal('total_value', 15, 2)->default(0)->comment('Valor total del stock (quantity * unit_cost)');
            
            // Fechas importantes
            $table->timestamp('last_received_at')->nullable()->comment('Última fecha de recepción');
            $table->timestamp('last_sold_at')->nullable()->comment('Última fecha de venta');
            $table->timestamp('last_counted_at')->nullable()->comment('Última fecha de conteo físico');
            
            // Estado y control
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false)->comment('Si es la ubicación principal del producto');
            $table->text('notes')->nullable()->comment('Notas específicas para este stock');
            
            // Índices únicos y compuestos
            $table->unique(['product_id', 'warehouse_id'], 'stock_product_warehouse_unique');
            $table->index('product_id');
            $table->index('warehouse_id');
            $table->index('quantity');
            $table->index('is_active');
            $table->index('is_primary');
            $table->index('location');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stocks');
    }
};
