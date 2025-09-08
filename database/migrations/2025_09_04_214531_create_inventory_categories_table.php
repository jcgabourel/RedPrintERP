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
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('color', 7)->nullable()->comment('Color en formato HEX para UI');
            $table->string('icon', 50)->nullable()->comment('Ícono para representar la categoría');
            
            // Índices
            $table->index('parent_id');
            $table->index('is_active');
            $table->index('sort_order');
            
            // Foreign key para categorías padre
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('inventory_categories')
                  ->onDelete('set null');
                  
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_categories');
    }
};
