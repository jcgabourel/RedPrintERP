<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Juan Pérez García',
                'rfc' => 'PEGJ800101123',
                'email' => 'juan.perez@email.com',
                'phone' => '555-123-4567',
                'address' => 'Av. Insurgentes 123, Ciudad de México'
            ],
            [
                'name' => 'María González López',
                'rfc' => 'GOLM850505456',
                'email' => 'maria.gonzalez@email.com',
                'phone' => '555-987-6543',
                'address' => 'Calle Reforma 456, Guadalajara'
            ],
            [
                'name' => 'Carlos Rodríguez Martínez',
                'rfc' => 'ROMC900909789',
                'email' => 'carlos.rodriguez@email.com',
                'phone' => '555-456-7890',
                'address' => 'Blvd. Díaz Ordaz 789, Monterrey'
            ],
            [
                'name' => 'Ana Sánchez Hernández',
                'rfc' => 'SAHA750303321',
                'email' => 'ana.sanchez@email.com',
                'phone' => '555-234-5678',
                'address' => 'Paseo de la Reforma 321, Puebla'
            ],
            [
                'name' => 'Pedro Ramírez Flores',
                'rfc' => 'RAFP820202654',
                'email' => 'pedro.ramirez@email.com',
                'phone' => '555-876-5432',
                'address' => 'Av. Universidad 654, Querétaro'
            ]
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        $this->command->info('Clientes de prueba creados exitosamente!');
    }
}