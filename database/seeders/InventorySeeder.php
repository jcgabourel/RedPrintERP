<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // Insertar categorías
        $categories = [
            ['name' => 'Electrónicos', 'slug' => 'electronicos', 'description' => 'Dispositivos electrónicos y gadgets', 'is_active' => true],
            ['name' => 'Ropa', 'slug' => 'ropa', 'description' => 'Prendas de vestir', 'is_active' => true],
            ['name' => 'Hogar', 'slug' => 'hogar', 'description' => 'Artículos para el hogar', 'is_active' => true],
            ['name' => 'Deportes', 'slug' => 'deportes', 'description' => 'Artículos deportivos', 'is_active' => true],
            ['name' => 'Libros', 'slug' => 'libros', 'description' => 'Libros y material educativo', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            DB::table('inventory_categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Insertar marcas
        $brands = [
            ['name' => 'Samsung', 'slug' => 'samsung', 'description' => 'Tecnología coreana', 'is_active' => true],
            ['name' => 'Apple', 'slug' => 'apple', 'description' => 'Tecnología premium', 'is_active' => true],
            ['name' => 'Nike', 'slug' => 'nike', 'description' => 'Deportes y ropa', 'is_active' => true],
            ['name' => 'Adidas', 'slug' => 'adidas', 'description' => 'Ropa deportiva', 'is_active' => true],
            ['name' => 'Sony', 'slug' => 'sony', 'description' => 'Electrónicos', 'is_active' => true],
        ];

        foreach ($brands as $brand) {
            DB::table('inventory_brands')->insert(array_merge($brand, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Insertar unidades de medida
        $units = [
            ['name' => 'Pieza', 'abbreviation' => 'pz', 'is_active' => true],
            ['name' => 'Kilogramo', 'abbreviation' => 'kg', 'is_active' => true],
            ['name' => 'Litro', 'abbreviation' => 'lt', 'is_active' => true],
            ['name' => 'Metro', 'abbreviation' => 'm', 'is_active' => true],
            ['name' => 'Caja', 'abbreviation' => 'caja', 'is_active' => true],
        ];

        foreach ($units as $unit) {
            DB::table('inventory_units')->insert(array_merge($unit, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Insertar almacén principal
        DB::table('inventory_warehouses')->insert([
            'name' => 'Almacén Principal',
            'code' => 'ALM-001',
            'type' => 'physical',
            'address' => 'Av. Insurgentes Sur 123',
            'city' => 'Ciudad de México',
            'state' => 'CDMX',
            'zip_code' => '03100',
            'country' => 'México',
            'contact_name' => 'Juan Pérez',
            'contact_phone' => '+525512345678',
            'contact_email' => 'almacen@redprinterp.com',
            'area_square_meters' => 500.00,
            'storage_capacity' => 10000,
            'is_active' => true,
            'is_default' => true,
            'notes' => 'Almacén principal de la empresa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar algunos productos de ejemplo
        $products = [
            [
                'sku' => 'PROD-001',
                'name' => 'Smartphone Samsung Galaxy S23',
                'slug' => 'smartphone-samsung-galaxy-s23',
                'description' => 'Teléfono inteligente de última generación',
                'category_id' => 1,
                'brand_id' => 1,
                'unit_id' => 1,
                'cost_price' => 12000.00,
                'selling_price' => 15999.00,
                'current_stock' => 25,
                'min_stock' => 5,
                'max_stock' => 100,
                'track_stock' => true,
                'is_active' => true,
                'weight' => 0.168,
                'length' => 15.0,
                'width' => 7.0,
                'height' => 0.8,
            ],
            [
                'sku' => 'PROD-002',
                'name' => 'iPhone 14 Pro',
                'slug' => 'iphone-14-pro',
                'description' => 'iPhone de alta gama con cámara profesional',
                'category_id' => 1,
                'brand_id' => 2,
                'unit_id' => 1,
                'cost_price' => 18000.00,
                'selling_price' => 23999.00,
                'current_stock' => 15,
                'min_stock' => 3,
                'max_stock' => 50,
                'track_stock' => true,
                'is_active' => true,
                'weight' => 0.206,
                'length' => 14.7,
                'width' => 7.1,
                'height' => 0.78,
            ],
            [
                'sku' => 'PROD-003',
                'name' => 'Tenis Nike Air Max',
                'slug' => 'tenis-nike-air-max',
                'description' => 'Tenis deportivos de alta calidad',
                'category_id' => 2,
                'brand_id' => 3,
                'unit_id' => 1,
                'cost_price' => 1500.00,
                'selling_price' => 2499.00,
                'current_stock' => 42,
                'min_stock' => 10,
                'max_stock' => 200,
                'track_stock' => true,
                'is_active' => true,
                'weight' => 0.8,
                'length' => 30.0,
                'width' => 20.0,
                'height' => 12.0,
            ],
            [
                'sku' => 'PROD-004',
                'name' => 'PlayStation 5',
                'slug' => 'playstation-5',
                'description' => 'Consola de videojuegos de última generación',
                'category_id' => 1,
                'brand_id' => 5,
                'unit_id' => 1,
                'cost_price' => 9000.00,
                'selling_price' => 11999.00,
                'current_stock' => 8,
                'min_stock' => 2,
                'max_stock' => 30,
                'track_stock' => true,
                'is_active' => true,
                'weight' => 4.5,
                'length' => 39.0,
                'width' => 26.0,
                'height' => 10.4,
            ],
            [
                'sku' => 'PROD-005',
                'name' => 'Sudadera Adidas Originals',
                'slug' => 'sudadera-adidas-originals',
                'description' => 'Sudadera deportiva de alta calidad',
                'category_id' => 2,
                'brand_id' => 4,
                'unit_id' => 1,
                'cost_price' => 600.00,
                'selling_price' => 999.00,
                'current_stock' => 0,
                'min_stock' => 5,
                'max_stock' => 100,
                'track_stock' => true,
                'is_active' => true,
                'weight' => 0.5,
                'length' => 70.0,
                'width' => 50.0,
                'height' => 3.0,
            ],
        ];

        foreach ($products as $product) {
            DB::table('inventory_products')->insert(array_merge($product, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Datos de inventario insertados correctamente.');
    }
}