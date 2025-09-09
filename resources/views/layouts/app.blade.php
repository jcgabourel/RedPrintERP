<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RedPrintERP - Sistema de Gestión</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <style>
        .sidebar {
            width: 250px;
            transition: all 0.3s;
        }
        .sidebar.collapsed {
            width: 64px;
        }
        .main-content {
            margin-left: 250px;
            transition: all 0.3s;
        }
        .main-content.expanded {
            margin-left: 64px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="flex h-screen">
        <!-- Sidebar -->
        <div :class="sidebarCollapsed ? 'sidebar collapsed' : 'sidebar'" class="sidebar bg-gray-800 text-white fixed left-0 top-0 h-full z-50">
            <div class="p-4 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <h1 x-show="!sidebarCollapsed" class="text-xl font-bold">RedPrintERP</h1>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="text-gray-400 hover:text-white">
                        <i :class="sidebarCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
                    </button>
                </div>
            </div>
            
            <nav class="mt-4">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span x-show="!sidebarCollapsed" class="ml-3">Dashboard</span>
                </a>
                
                <a href="{{ route('customers.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white">
                    <i class="fas fa-users w-6"></i>
                    <span x-show="!sidebarCollapsed" class="ml-3">Clientes</span>
                </a>
                
                <!-- Inventario Dropdown -->
                <div x-data="{ inventoryOpen: false }" class="relative">
                    <button @click="inventoryOpen = !inventoryOpen" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white">
                        <div class="flex items-center">
                            <i class="fas fa-box w-6"></i>
                            <span x-show="!sidebarCollapsed" class="ml-3">Inventario</span>
                        </div>
                        <i :class="inventoryOpen ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" x-show="!sidebarCollapsed" class="ml-2 text-sm"></i>
                    </button>
                    
                    <!-- Submenu -->
                    <div x-show="inventoryOpen && !sidebarCollapsed" class="bg-gray-900 ml-6 mt-1 rounded-md overflow-hidden">
                        <a href="{{ route('inventory.products.index') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white text-sm">
                            <i class="fas fa-cube w-4 mr-2"></i>
                            Productos
                        </a>
                        <a href="{{ route('inventory.categories.index') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white text-sm">
                            <i class="fas fa-tags w-4 mr-2"></i>
                            Categorías
                        </a>
                        <a href="{{ route('inventory.brands.index') }}" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white text-sm">
                            <i class="fas fa-copyright w-4 mr-2"></i>
                            Marcas
                        </a>
                    </div>
                </div>
                
                <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white">
                    <i class="fas fa-shopping-cart w-6"></i>
                    <span x-show="!sidebarCollapsed" class="ml-3">Ventas</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div :class="sidebarCollapsed ? 'main-content expanded' : 'main-content'" class="main-content flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 mr-4">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h2 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <button class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bell"></i>
                        </button>
                        <div class="relative">
                            <button class="flex items-center space-x-2 text-gray-600 hover:text-gray-800">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="hidden md:block">Usuario</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-6">
                @yield('content')
            </main>
        </div>

        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>
    </div>

    <script>
        // Enhanced JavaScript for API interactions
        const API_BASE = '/api';
        
        async function apiRequest(endpoint, options = {}) {
            const config = {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...options.headers
                },
                ...options
            };
            
            // Add CSRF token for non-GET requests
            if (config.method && config.method !== 'GET') {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                if (token) {
                    config.headers['X-CSRF-TOKEN'] = token;
                }
            }
            
            try {
                const response = await fetch(`${API_BASE}${endpoint}`, config);
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            } catch (error) {
                console.error('API Request failed:', error);
                throw error;
            }
        }
        
        // Enhanced Customer API functions with better error handling
        const CustomerAPI = {
            getAll: () => apiRequest('/customers'),
            getById: (id) => apiRequest(`/customers/${id}`),
            create: (data) => apiRequest('/customers', {
                method: 'POST',
                body: JSON.stringify(data)
            }),
            update: (id, data) => apiRequest(`/customers/${id}`, {
                method: 'PUT',
                body: JSON.stringify(data)
            }),
            delete: (id) => apiRequest(`/customers/${id}`, {
                method: 'DELETE'
            }),
            search: (name) => apiRequest(`/customers/search?name=${encodeURIComponent(name)}`)
        };
        
        // Inventory API functions
        const InventoryAPI = {
            // Products
            getProducts: (params = {}) => {
                const queryParams = new URLSearchParams(params).toString();
                return apiRequest(`/inventory/products${queryParams ? '?' + queryParams : ''}`);
            },
            getProduct: (id) => apiRequest(`/inventory/products/${id}`),
            createProduct: (data) => apiRequest('/inventory/products', {
                method: 'POST',
                body: JSON.stringify(data)
            }),
            updateProduct: (id, data) => apiRequest(`/inventory/products/${id}`, {
                method: 'PUT',
                body: JSON.stringify(data)
            }),
            deleteProduct: (id) => apiRequest(`/inventory/products/${id}`, {
                method: 'DELETE'
            }),
            getProductStats: () => apiRequest('/inventory/products/stats'),
            
            // Categories
            getCategories: () => apiRequest('/inventory/categories'),
            createCategory: (data) => apiRequest('/inventory/categories', {
                method: 'POST',
                body: JSON.stringify(data)
            }),
            
            // Brands
            getBrands: () => apiRequest('/inventory/brands'),
            createBrand: (data) => apiRequest('/inventory/brands', {
                method: 'POST',
                body: JSON.stringify(data)
            }),
            
            // Dashboard
            getDashboard: () => apiRequest('/inventory/dashboard')
        };

        // Utility functions
        function showNotification(message, type = 'success') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 fade-in ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                type === 'warning' ? 'bg-yellow-500 text-black' :
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
        
        function formatRFC(rfc) {
            if (!rfc) return '';
            return rfc.toUpperCase().replace(/[^A-Z0-9]/g, '');
        }
        
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        function validatePhone(phone) {
            const phoneRegex = /^[\d\s\-\+\(\)]{10,20}$/;
            return phoneRegex.test(phone.replace(/\s/g, ''));
        }
    </script>
</body>
</html>