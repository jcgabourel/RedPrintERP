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
        Schema::create('inventory_units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('Nombre de la unidad (ej: Pieza, Metro, Litro)');
            $table->string('abbreviation', 10)->unique()->comment('Abreviatura (ej: pz, m, l)');
            $table->string('unit_type', 20)->default('count')->comment('Tipo: count, length, volume, weight, time');
            $table->decimal('conversion_factor', 15, 6)->default(1)->comment('Factor de conversión a unidad base');
            $table->boolean('is_base_unit')->default(false)->comment('Si es la unidad base para conversiones');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            // Índices
            $table->index('unit_type');
            $table->index('is_base_unit');
            $table->index('is_active');
            $table->index('sort_order');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_units');
    }
};
