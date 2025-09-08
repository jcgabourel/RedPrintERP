@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
<div x-data="productManagement()" class="max-w-7xl mx-auto">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Productos</h1>
        <div class="flex space-x-3">
            <button @click="exportProducts()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-download mr-2"></i>
                Exportar
            </button>
            <button @click="showCreateModal = true" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Nuevo Producto
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-box text-blue-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Productos</h3>
                    <p class="text-2xl font-bold text-gray-800" x-text="stats.totalProducts || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Activos</h3>
                    <p class="text-2xl font-bold text-gray-800" x-text="stats.activeProducts || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Stock Bajo</h3>
                    <p class="text-2xl font-bold text-gray-800" x-text="stats.lowStockProducts || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-times-circle text-red-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Sin Stock</h3>
                    <p class="text-2xl font-bold text-gray-800" x-text="stats.outOfStockProducts || 0"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="form-label">Buscar</label>
                <input 
                    x-model="filters.search" 
                    @input.debounce.500ms="loadProducts()"
                    type="text" 
                    placeholder="SKU, nombre, descripción..." 
                    class="form-input"
                >
            </div>
            
            <div>
                <label class="form-label">Categoría</label>
                <select x-model="filters.category" @change="loadProducts()" class="form-select">
                    <option value="">Todas las categorías</option>
                    <template x-for="category in categories" :key="category.id">
                        <option :value="category.id" x-text="category.name"></option>
                    </template>
                </select>
            </div>
            
            <div>
                <label class="form-label">Marca</label>
                <select x-model="filters.brand" @change="loadProducts()" class="form-select">
                    <option value="">Todas las marcas</option>
                    <template x-for="brand in brands" :key="brand.id">
                        <option :value="brand.id" x-text="brand.name"></option>
                    </template>
                </select>
            </div>
            
            <div>
                <label class="form-label">Estado</label>
                <select x-model="filters.status" @change="loadProducts()" class="form-select">
                    <option value="">Todos</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                    <option value="low_stock">Stock Bajo</option>
                    <option value="out_of_stock">Sin Stock</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div x-show="loading" class="p-8 text-center">
            <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
            <p class="mt-2 text-gray-600">Cargando productos...</p>
        </div>

        <div x-show="!loading && products.length === 0" class="p-8 text-center">
            <i class="fas fa-box text-gray-300 text-4xl mb-4"></i>
            <p class="text-gray-600">No hay productos registrados</p>
            <button @click="showCreateModal = true" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                Crear primer producto
            </button>
        </div>

        <div x-show="!loading && products.length > 0" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="product in products" :key="product.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <template x-if="product.image_url">
                                            <img :src="product.image_url" :alt="product.name" class="h-10 w-10 rounded-lg object-cover">
                                        </template>
                                        <template x-if="!product.image_url">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </template>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="product.name"></div>
                                        <div class="text-sm text-gray-500" x-text="product.brand?.name || 'Sin marca'"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-mono" x-text="product.sku"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500" x-text="product.category?.name || 'Sin categoría'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="product.current_stock"></div>
                                <template x-if="product.min_stock > 0">
                                    <div class="text-xs text-gray-500" x-text="`Mín: ${product.min_stock}`"></div>
                                </template>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="formatCurrency(product.selling_price)"></div>
                                <div class="text-xs text-gray-500" x-text="formatCurrency(product.cost_price)"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <template x-if="!product.is_active">
                                    <span class="badge badge-error">Inactivo</span>
                                </template>
                                <template x-if="product.is_active && product.current_stock === 0">
                                    <span class="badge badge-error">Sin Stock</span>
                                </template>
                                <template x-if="product.is_active && product.current_stock > 0 && product.current_stock <= product.min_stock">
                                    <span class="badge badge-warning">Stock Bajo</span>
                                </template>
                                <template x-if="product.is_active && product.current_stock > product.min_stock">
                                    <span class="badge badge-success">Disponible</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="editProduct(product)" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="viewProduct(product)" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="deleteProduct(product)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div x-show="!loading && products.length > 0" class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Mostrando <span x-text="pagination.from"></span> a <span x-text="pagination.to"></span> de 
                    <span x-text="pagination.total"></span> resultados
                </div>
                <div class="flex space-x-2">
                    <button @click="previousPage()" :disabled="pagination.current_page === 1" 
                        class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Anterior
                    </button>
                    <button @click="nextPage()" :disabled="pagination.current_page === pagination.last_page" 
                        class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function productManagement() {
    return {
        loading: false,
        products: [],
        categories: [],
        brands: [],
        stats: {},
        filters: {
            search: '',
            category: '',
            brand: '',
            status: ''
        },
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 15,
            total: 0,
            from: 0,
            to: 0
        },
        
        init() {
            this.loadProducts();
            this.loadCategories();
            this.loadBrands();
            this.loadStats();
        },
        
        async loadProducts() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    ...this.filters
                });
                
                const response = await fetch(`/api/inventory/products?${params}`);
                const data = await response.json();
                
                this.products = data.data || [];
                this.pagination = {
                    current_page: data.current_page || 1,
                    last_page: data.last_page || 1,
                    per_page: data.per_page || 15,
                    total: data.total || 0,
                    from: data.from || 0,
                    to: data.to || 0
                };
            } catch (error) {
                console.error('Error loading products:', error);
                showNotification('Error al cargar los productos', 'error');
            } finally {
                this.loading = false;
            }
        },
        
        async loadCategories() {
            try {
                const response = await fetch('/api/inventory/categories');
                const data = await response.json();
                this.categories = data.data || [];
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        },
        
        async loadBrands() {
            try {
                const response = await fetch('/api/inventory/brands');
                const data = await response.json();
                this.brands = data.data || [];
            } catch (error) {
                console.error('Error loading brands:', error);
            }
        },
        
        async loadStats() {
            try {
                const response = await fetch('/api/inventory/products/stats');
                const data = await response.json();
                this.stats = data.data || {};
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },
        
        nextPage() {
            if (this.pagination.current_page < this.pagination.last_page) {
                this.pagination.current_page++;
                this.loadProducts();
            }
        },
        
        previousPage() {
            if (this.pagination.current_page > 1) {
                this.pagination.current_page--;
                this.loadProducts();
            }
        },
        
        editProduct(product) {
            // Implementar edición de producto
            showNotification('Función de edición en desarrollo', 'info');
        },
        
        viewProduct(product) {
            // Implementar vista detallada
            showNotification('Función de visualización en desarrollo', 'info');
        },
        
        deleteProduct(product) {
            // Implementar eliminación
            showNotification('Función de eliminación en desarrollo', 'info');
        },
        
        exportProducts() {
            showNotification('Función de exportación en desarrollo', 'info');
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN'
            }).format(amount);
        }
    };
}
</script>
</div>
@endsection