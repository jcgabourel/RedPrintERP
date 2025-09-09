
@extends('layouts.app')

@section('title', 'Gestión de Categorías - Inventario')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Categorías</h1>
        <button @click="showCreateModal = true" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nueva Categoría
        </button>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" x-model="searchTerm" placeholder="Buscar categorías..." 
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría Padre</label>
                <select x-model="parentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas</option>
                    <option value="root">Solo raíz</option>
                    <option value="subcategories">Con subcategorías</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de categorías -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Padre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="category in filteredCategories" :key="category.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900" x-text="category.name"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500" x-text="category.slug"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500" x-text="category.parent?.name || 'Raíz'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800" 
                                      x-text="category.products_count || 0"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span x-bind:class="{
                                    'bg-green-100 text-green-800': category.is_active,
                                    'bg-red-100 text-red-800': !category.is_active
                                }" class="px-2 py-1 text-xs font-semibold rounded-full" 
                                x-text="category.is_active ? 'Activa' : 'Inactiva'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="editCategory(category)" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                <button @click="confirmDelete(category)" class="text-red-600 hover:text-red-900">Eliminar</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6" 
             x-show="categories.meta && categories.meta.total > categories.meta.per_page">
            <div class="flex-1 flex justify-between sm:hidden">
                <button @click="prevPage" :disabled="!categories.links.prev" 
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Anterior
                </button>
                <button @click="nextPage" :disabled="!categories.links.next" 
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Siguiente
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Mostrando
                        <span class="font-medium" x-text="categories.meta.from"></span>
                        a
                        <span class="font-medium" x-text="categories.meta.to"></span>
                        de
                        <span class="font-medium" x-text="categories.meta.total"></span>
                        resultados
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button @click="prevPage" :disabled="!categories.links.prev" 
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Anterior</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <template x-for="page in categories.meta.last_page" :key="page">
                            <button @click="goToPage(page)" 
                                    :class="{
                                        'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': page === categories.meta.current_page,
                                        'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': page !== categories.meta.current_page
                                    }" 
                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                <span x-text="page"></span>
                            </button>
                        </template>
                        <button @click="nextPage" :disabled="!categories.links.next" 
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

<!-- Modal para crear/editar categoría -->
<div x-show="showCreateModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="showEditModal ? 'Editar Categoría' : 'Nueva Categoría'"></h3>
            
            <form @submit.prevent="showEditModal ? updateCategory() : createCategory()">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría Padre</label>
                        <select x-model="form.parent_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar categoría padre</option>
                            <template x-for="cat in categories.data" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" x-model="form.is_active" id="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Categoría activa</label>
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
            <p class="text-sm text-gray-500 mb-4">¿Estás seguro de que quieres eliminar la categoría "<span x-text="categoryToDelete?.name"></span>"? Esta acción no se puede deshacer.</p>
            
            <div class="flex justify-end space-x-3">
                <button @click="showDeleteModal = false" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancelar
                </button>
                <button @click="deleteCategory" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('categories', () => ({
        categories: [],
        searchTerm: '',
        statusFilter: '',
        parentFilter: '',
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        categoryToDelete: null,
        form: {
            id: null,
            name: '',
            description: '',
            parent_id: '',
            is_active: true
        },
        
        async init() {
            await this.loadCategories();
        },
        
        async loadCategories(page = 1) {
            try {
                let url = `/api/inventory/categories?page=${page}`;
                
                if (this.searchTerm) {
                    url += `&search=${this.searchTerm}`;
                }
                if (this.statusFilter) {
                    url += `&status=${this.statusFilter}`;
                }
                if (this.parentFilter) {
                    url += `&parent=${this.parentFilter}`;
                }
                
                const response = await fetch(url);
                this.categories = await response.json();
            } catch (error) {
                console.error('Error loading categories:', error);
                alert('Error al cargar las categorías');
            }
        },
        
        get filteredCategories() {
            if (!this.categories.data) return [];
            
            return this.categories.data.filter(category => {
                let matches = true;
                
                if (this.searchTerm) {
                    matches = matches && category.name.toLowerCase().includes(this.searchTerm.toLowerCase());
                }
                
                if (this.statusFilter === 'active') {
                    matches = matches && category.is_active;
                } else if (this.statusFilter === 'inactive') {
                    matches = matches && !category.is_active;
                }
                
                if (this.parentFilter === 'root') {
                    matches = matches && !category.parent_id;
                } else if (this.parentFilter === 'subcategories') {
                    matches = matches && category.parent_id;
                }
                
                return matches;
            });
        },
        
        async createCategory() {
            try {
                const response = await fetch('/api/inventory/categories', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.form)
                });
                
                if (response.ok) {
                    this.closeModal();
                    await this.loadCategories();
                    alert('Categoría creada exitosamente');
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Error al crear la categoría'));
                }
            } catch (error) {
                console.error('Error creating category:', error);
                alert('Error al crear la categoría');
            }
        },
        
        editCategory(category) {
            this.form = {
                id: category.id,
                name: category.name,
                description: category.description || '',
                parent_id: category.parent_id || '',
                is_active: category.is_active
            };
            this.showEditModal = true;
        },
        
        async updateCategory() {
            try {
                const response = await fetch(`/api/inventory/categories/${this.form.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                   },
                   body: JSON.stringify(this.form)
               });
               
               if (response.ok) {
                   this.closeModal();
                   await this.loadCategories();
                   alert('Categoría actualizada exitosamente');
               } else {
                   const error = await response.json();
                   alert('Error: ' + (error.message || 'Error al actualizar la categoría'));
               }
           } catch (error) {
               console.error('Error updating category:', error);
               alert('Error al actualizar la categoría');
           }
       },
       
       confirmDelete(category) {
           this.categoryToDelete = category;
           this.showDeleteModal = true;
       },
       
       async deleteCategory() {
           try {
               const response = await fetch(`/api/inventory/categories/${this.categoryToDelete.id}`, {
                   method: 'DELETE',
                   headers: {
                       'X-CSRF-TOKEN': '{{ csrf_token() }}'
                   }
               });
               
               if (response.ok) {
                   this.showDeleteModal = false;
                   await this.loadCategories();
                   alert('Categoría eliminada exitosamente');
               } else {
                   const error = await response.json();
                   alert('Error: ' + (error.message || 'Error al eliminar la categoría'));
               }
           } catch (error) {
               console.error('Error deleting category:', error);
               alert('Error al eliminar la categoría');
           }
       },
       
       closeModal() {
           this.showCreateModal = false;
           this.showEditModal = false;
           this.form = {
               id: null,
               name: '',
               description: '',
               parent_id: '',
               is_active: true
           };
       },
       
       prevPage() {
           if (this.categories.links.prev) {
               const url = new URL(this.categories.links.prev);
               const page = url.searchParams.get('page');
               this.loadCategories(page);
           }
       },
       
       nextPage() {
           if (this.categories.links.next) {
               const url = new URL(this.categories.links.next);
               const page = url.searchParams.get('page');
               this.loadCategories(page);
           }
       },
       
       goToPage(page) {
           this.loadCategories(page);
       }
   }));
});
</script>
@endsection