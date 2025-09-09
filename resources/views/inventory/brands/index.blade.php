
@extends('layouts.app')

@section('title', 'Gestión de Marcas - Inventario')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Marcas</h1>
        <button @click="showCreateModal = true" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nueva Marca
        </button>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" x-model="searchTerm" placeholder="Buscar marcas..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select x-model="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="active">Activas</option>
                    <option value="inactive">Inactivas</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
                <input type="text" x-model="countryFilter" placeholder="Filtrar por país..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contacto</label>
                <input type="text" x-model="contactFilter" placeholder="Filtrar por contacto..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- Tabla de marcas -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">País</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="brand in filteredBrands" :key="brand.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900" x-text="brand.name"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500" x-text="brand.slug"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500" x-text="brand.country || 'N/A'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500" x-text="brand.contact_info || 'N/A'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800" 
                                      x-text="brand.products_count || 0"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span x-bind:class="{
                                    'bg-green-100 text-green-800': brand.is_active,
                                    'bg-red-100 text-red-800': !brand.is_active
                                }" class="px-2 py-1 text-xs font-semibold rounded-full" 
                                x-text="brand.is_active ? 'Activa' : 'Inactiva'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="editBrand(brand)" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                <button @click="confirmDelete(brand)" class="text-red-600 hover:text-red-900">Eliminar</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6" 
             x-show="brands.meta && brands.meta.total > brands.meta.per_page">
            <div class="flex-1 flex justify-between sm:hidden">
                <button @click="prevPage" :disabled="!brands.links.prev" 
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Anterior
                </button>
                <button @click="nextPage" :disabled="!brands.links.next" 
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Siguiente
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Mostrando
                        <span class="font-medium" x-text="brands.meta.from"></span>
                        a
                        <span class="font-medium" x-text="brands.meta.to"></span>
                        de
                        <span class="font-medium" x-text="brands.meta.total"></span>
                        resultados
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button @click="prevPage" :disabled="!brands.links.prev" 
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Anterior</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <template x-for="page in brands.meta.last_page" :key="page">
                            <button @click="goToPage(page)" 
                                    :class="{
                                        'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': page === brands.meta.current_page,
                                        'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': page !== brands.meta.current_page
                                    }" 
                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                <span x-text="page"></span>
                            </button>
                        </template>
                        <button @click="nextPage" :disabled="!brands.links.next" 
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Siguiente</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar marca -->
<div x-show="showCreateModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="showEditModal ? 'Editar Marca' : 'Nueva Marca'"></h3>
            
            <form @submit.prevent="showEditModal ? updateBrand() : createBrand()">
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" x-model="form.name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea x-model="form.description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
                        <input type="text" x-model="form.country" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Información de Contacto</label>
                        <textarea x-model="form.contact_info" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" x-model="form.is_active" id="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Marca activa</label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="closeModal" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span x-text="showEditModal ? 'Actualizar' : 'Crear'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div x-show="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmar eliminación</h3>
            <p class="text-sm text-gray-500 mb-4">¿Estás seguro de que quieres eliminar la marca "<span x-text="brandToDelete?.name"></span>"? Esta acción no se puede deshacer.</p>
            
            <div class="flex justify-end space-x-3">
                <button @click="showDeleteModal = false" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancelar
                </button>
                <button @click="deleteBrand" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('brands', () => ({
        brands: [],
        searchTerm: '',
        statusFilter: '',
        countryFilter: '',
        contactFilter: '',
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        brandToDelete: null,
        form: {
            id: null,
            name: '',
            description: '',
            country: '',
            contact_info: '',
            is_active: true
        },
        
        async init() {
            await this.loadBrands();
        },
        
        async loadBrands(page = 1) {
            try {
                let url = `/api/inventory/brands?page=${page}`;
                
                if (this.searchTerm) {
                    url += `&search=${this.searchTerm}`;
                }
                if (this.statusFilter) {
                    url += `&status=${this.statusFilter}`;
                }
                if (this.countryFilter) {
                    url += `&country=${this.countryFilter}`;
                }
                if (this.contactFilter) {
                    url += `&contact=${this.contactFilter}`;
                }
                
                const response = await fetch(url);
                this.brands = await response.json();
            } catch (error) {
                console.error('Error loading brands:', error);
                alert('Error al cargar las marcas');
            }
        },
        
        get filteredBrands() {
            if (!this.brands.data) return [];
            
            return this.brands.data.filter(brand => {
                let matches = true;
                
                if (this.searchTerm) {
                    matches = matches && brand.name.toLowerCase().includes(this.searchTerm.toLowerCase());
                }
                
                if (this.statusFilter === 'active') {
                    matches = matches && brand.is_active;
                } else if (this.statusFilter === 'inactive') {
                    matches = matches && !brand.is_active;
                }
                
                if (this.countryFilter) {
                    matches = matches && brand.country?.toLowerCase().includes(this.countryFilter.toLowerCase());
                }
                
                if (this.contactFilter) {
                    matches = matches && brand.contact_info?.toLowerCase().includes(this.contactFilter.toLowerCase());
                }
                
                return matches;
            });
        },
        
        async createBrand() {
            try {
                const response = await fetch('/api/inventory/brands', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.form)
                });
                
                if (response.ok) {
                    this.closeModal();
                    await this.loadBrands();
                    alert('Marca creada exitosamente');
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Error al crear la marca'));
                }
            } catch (error) {
                console.error('Error creating brand:', error);
                alert('Error al crear la marca');
            }
        },
        
        editBrand(brand) {
            this.form = {
                id: brand.id,
                name: brand.name,
                description: brand.description || '',
                country: brand.country || '',
                contact_info: brand.contact_info || '',
                is_active: brand.is_active
            };
            this.showEditModal = true;
        },
        
        async updateBrand() {
            try {
                const response = await fetch(`/api/inventory/brands/${this.form.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.form)
                });
                
                if (response.ok) {
                    this.closeModal();
                    await this.loadBrands();
                    alert('Marca actualizada exitosamente');
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Error al actualizar la marca'));
                }
            } catch (error) {
                console.error('Error updating brand:', error);
                alert('Error al actualizar la marca');
            }
        },
        
        confirmDelete(brand) {
            this.brandToDelete = brand;
            this.showDeleteModal = true;
        },
        
        async deleteBrand() {
            try {
                const response = await fetch(`/api/inventory/brands/${this.brandToDelete.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                if (response.ok) {
                    this.showDeleteModal = false;
                    await this.loadBrands();
                    alert('Marca eliminada exitosamente');
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Error al eliminar la marca'));
                }
            } catch (error) {
                console.error('Error deleting brand:', error);
                alert('Error al eliminar la marca');
            }
        },
        
        closeModal() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.form = {
                id: null,
                name: '',
                description: '',
                country: '',
                contact_info: '',
                is_active: true
            };
        },
        
        prevPage() {
            if (this.brands.links.prev) {
                const url = new URL(this.brands.links.prev);
                const page = url.searchParams.get('page');
                this.loadBrands(page);
            }
        },
        
        nextPage() {
            if (this.brands.links.next) {
                const url = new URL(this.brands.links.next);
                const page = url.searchParams.get('page');
                this.loadBrands(page);
            }
        },
        
        goToPage(page) {
            this.loadBrands(page);
        }
    }));
});
</script>
@endsection