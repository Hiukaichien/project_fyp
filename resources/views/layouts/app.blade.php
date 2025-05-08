<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome CDN -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Add x-cloak utility if not already present in your app.css --}}
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900"> {{-- Added dark:bg-gray-900 for consistency if you use dark mode elsewhere --}}
            @include('layouts.navigation')

            {{-- This div will handle the offset for the fixed sidebar/topbar --}}
            <div class="sm:ml-64"> {{-- Margin for desktop sidebar (width w-64) --}}
                <!-- Page Heading -->
                @isset($header)
                    {{-- pt-16 for mobile top bar (h-16), sm:pt-0 to remove padding when desktop sidebar is active --}}
                    <header class="bg-white shadow pt-16 sm:pt-0">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    {{-- If there's no header, content needs padding for mobile top bar --}}
                    <div class="{{ isset($header) ? '' : 'pt-16 sm:pt-0' }}">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>