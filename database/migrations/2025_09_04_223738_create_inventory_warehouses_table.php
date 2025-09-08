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
        Schema::create('inventory_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique()->comment('Código único del almacén');
            $table->string('type', 30)->default('physical')->comment('physical, virtual, consignment');
            $table->text('address')->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->string('country', 50)->nullable()->default('México');
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();
            $table->decimal('latitude', 10, 8)->nullable()->comment('Coordenada geográfica');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Coordenada geográfica');
            $table->decimal('area_square_meters', 10, 2)->nullable()->comment('Área total en m²');
            $table->integer('storage_capacity')->nullable()->comment('Capacidad máxima en unidades');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false)->comment('Si es el almacén por defecto');
            $table->json('operating_hours')->nullable()->comment('Horarios de operación en JSON');
            $table->text('notes')->nullable();
            
            // Índices
            $table->index('type');
            $table->index('is_active');
            $table->index('is_default');
            $table->index('city');
            $table->index('state');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_warehouses');
    }
};
