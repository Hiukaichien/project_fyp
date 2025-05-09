@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center w-full ps-3 pe-4 py-2 border-s-4 border-indigo-400 text-base font-medium text-indigo-100 bg-gray-900 focus:outline-none focus:text-indigo-50 focus:bg-gray-700 focus:border-indigo-300 transition duration-150 ease-in-out' // Active state for dark theme
            : 'flex items-center w-full ps-3 pe-4 py-2 border-s-4 border-transparent text-base font-medium text-gray-300 hover:text-gray-100 hover:bg-gray-700 hover:border-gray-600 focus:outline-none focus:text-gray-100 focus:bg-gray-700 focus:border-gray-600 transition duration-150 ease-in-out'; // Inactive state for dark theme
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>