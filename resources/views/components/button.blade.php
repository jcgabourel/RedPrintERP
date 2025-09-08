@props(['type' => 'button', 'variant' => 'primary', 'size' => 'default'])

@php
    $baseClasses = 'btn focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200';
    
    $variantClasses = [
        'primary' => 'bg-blue-500 hover:bg-blue-600 text-white focus:ring-blue-500',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-700 focus:ring-gray-500',
        'danger' => 'bg-red-500 hover:bg-red-600 text-white focus:ring-red-500',
        'success' => 'bg-green-500 hover:bg-green-600 text-white focus:ring-green-500',
        'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white focus:ring-yellow-500',
    ];
    
    $sizeClasses = [
        'default' => 'px-4 py-2',
        'sm' => 'px-3 py-1 text-sm',
        'lg' => 'px-6 py-3 text-lg',
    ];
    
    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>