@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Header -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Bienvenido a RedPrintERP</h1>
        <p class="text-gray-600">Sistema de gestión empresarial integrado</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Clientes</h3>
                    <p class="text-2xl font-bold text-gray-800" id="total-customers">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-box text-green-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Productos</h3>
                    <p class="text-2xl font-bold text-gray-800">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-shopping-cart text-purple-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Ventas Hoy</h3>
                    <p class="text-2xl font-bold text-gray-800">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-warehouse text-orange-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Inventario</h3>
                    <p class="text-2xl font-bold text-gray-800">0 items</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('customers.index') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <div class="p-3 bg-blue-100 rounded-full mb-3">
                    <i class="fas fa-user-plus text-blue-500 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Nuevo Cliente</span>
            </a>

            <a href="#" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <div class="p-3 bg-green-100 rounded-full mb-3">
                    <i class="fas fa-box-open text-green-500 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Nuevo Producto</span>
            </a>

            <a href="#" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <div class="p-3 bg-purple-100 rounded-full mb-3">
                    <i class="fas fa-cash-register text-purple-500 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Nueva Venta</span>
            </a>

            <a href="#" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                <div class="p-3 bg-orange-100 rounded-full mb-3">
                    <i class="fas fa-chart-line text-orange-500 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Reportes</span>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load customer count
    async function loadCustomerCount() {
        try {
            const response = await CustomerAPI.getAll();
            document.getElementById('total-customers').textContent = response.total || response.data?.length || 0;
        } catch (error) {
            console.error('Error loading customer count:', error);
        }
    }

    loadCustomerCount();
});
</script>
@endsection