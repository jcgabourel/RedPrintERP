@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@section('content')
<div x-data="customerManagement()" class="max-w-7xl mx-auto">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Clientes</h1>
        <button @click="showCreateModal = true" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Nuevo Cliente
        </button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input 
                    x-model="searchTerm" 
                    @input.debounce.500ms="searchCustomers()"
                    type="text" 
                    placeholder="Buscar clientes por nombre..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            <div class="flex space-x-2">
                <button @click="loadCustomers()" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div x-show="loading" class="p-8 text-center">
            <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
            <p class="mt-2 text-gray-600">Cargando clientes...</p>
        </div>

        <div x-show="!loading && customers.length === 0" class="p-8 text-center">
            <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
            <p class="text-gray-600">No hay clientes registrados</p>
            <button @click="showCreateModal = true" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                Crear primer cliente
            </button>
        </div>

        <div x-show="!loading && customers.length > 0" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RFC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="customer in customers" :key="customer.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900" x-text="customer.name"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500" x-text="customer.rfc"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500" x-text="customer.email"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500" x-text="customer.phone"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="editCustomer(customer)" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteCustomer(customer)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4" x-text="currentCustomer.id ? 'Editar Cliente' : 'Nuevo Cliente'"></h2>
                
                <form @submit.prevent="saveCustomer()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                            <input 
                                x-model="currentCustomer.name" 
                                type="text" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RFC *</label>
                            <input 
                                x-model="currentCustomer.rfc" 
                                type="text" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input 
                                x-model="currentCustomer.email" 
                                type="email" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                            <input 
                                x-model="currentCustomer.phone" 
                                type="tel" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección *</label>
                            <textarea 
                                x-model="currentCustomer.address" 
                                required 
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            ></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                            <span x-text="currentCustomer.id ? 'Actualizar' : 'Crear'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">Confirmar Eliminación</h2>
                <p class="text-gray-600 mb-6">¿Estás seguro de que quieres eliminar al cliente <span x-text="currentCustomer.name" class="font-semibold"></span>?</p>
                
                <div class="flex justify-end space-x-3">
                    <button @click="showDeleteModal = false" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Cancelar
                    </button>
                    <button @click="confirmDelete()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function customerManagement() {
    return {
        loading: false,
        customers: [],
        searchTerm: '',
        
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        
        currentCustomer: {
            id: null,
            name: '',
            rfc: '',
            email: '',
            phone: '',
            address: ''
        },
        
        init() {
            this.loadCustomers();
        },
        
        async loadCustomers() {
            this.loading = true;
            try {
                const response = await CustomerAPI.getAll();
                this.customers = response.data || [];
                showNotification('Clientes cargados correctamente', 'success');
            } catch (error) {
                console.error('Error loading customers:', error);
                showNotification('Error al cargar los clientes: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        async searchCustomers() {
            if (!this.searchTerm.trim()) {
                this.loadCustomers();
                return;
            }
            
            this.loading = true;
            try {
                const response = await CustomerAPI.search(this.searchTerm);
                this.customers = response.data || [];
                if (this.customers.length === 0) {
                    showNotification('No se encontraron clientes con ese nombre', 'warning');
                }
            } catch (error) {
                console.error('Error searching customers:', error);
                showNotification('Error al buscar clientes: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        editCustomer(customer) {
            this.currentCustomer = { ...customer };
            this.showEditModal = true;
        },
        
        deleteCustomer(customer) {
            this.currentCustomer = { ...customer };
            this.showDeleteModal = true;
        },
        
        validateCustomerForm() {
            if (!this.currentCustomer.name?.trim()) {
                showNotification('El nombre es requerido', 'error');
                return false;
            }
            
            if (!this.currentCustomer.rfc?.trim()) {
                showNotification('El RFC es requerido', 'error');
                return false;
            }
            
            if (!validateEmail(this.currentCustomer.email)) {
                showNotification('El email no tiene un formato válido', 'error');
                return false;
            }
            
            if (!validatePhone(this.currentCustomer.phone)) {
                showNotification('El teléfono no tiene un formato válido', 'error');
                return false;
            }
            
            if (!this.currentCustomer.address?.trim()) {
                showNotification('La dirección es requerida', 'error');
                return false;
            }
            
            return true;
        },
        
        async saveCustomer() {
            if (!this.validateCustomerForm()) {
                return;
            }
            
            try {
                // Format RFC to uppercase and remove special characters
                this.currentCustomer.rfc = formatRFC(this.currentCustomer.rfc);
                
                if (this.currentCustomer.id) {
                    await CustomerAPI.update(this.currentCustomer.id, this.currentCustomer);
                    showNotification('Cliente actualizado exitosamente', 'success');
                } else {
                    await CustomerAPI.create(this.currentCustomer);
                    showNotification('Cliente creado exitosamente', 'success');
                }
                
                this.closeModal();
                this.loadCustomers();
            } catch (error) {
                console.error('Error saving customer:', error);
                showNotification('Error al guardar el cliente: ' + error.message, 'error');
            }
        },
        
        async confirmDelete() {
            try {
                await CustomerAPI.delete(this.currentCustomer.id);
                this.showDeleteModal = false;
                this.loadCustomers();
                showNotification('Cliente eliminado exitosamente', 'success');
            } catch (error) {
                console.error('Error deleting customer:', error);
                showNotification('Error al eliminar el cliente: ' + error.message, 'error');
            }
        },
        
        closeModal() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.showDeleteModal = false;
            this.resetCurrentCustomer();
        },
        
        resetCurrentCustomer() {
            this.currentCustomer = {
                id: null,
                name: '',
                rfc: '',
                email: '',
                phone: '',
                address: ''
            };
        }
    };
}
</script>
</div>
@endsection