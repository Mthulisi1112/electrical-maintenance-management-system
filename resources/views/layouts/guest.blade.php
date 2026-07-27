<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EMMS') - Electrical Maintenance Management System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    {{-- Simple header for guest pages with grey theme --}}
    <div class="min-h-screen flex flex-col">
        {{-- Navigation with grey theme matching main app --}}
        <nav class="bg-gradient-to-b from-gray-900 to-gray-950 border-b border-gray-800 sticky top-0 z-50">
            <div class="h-px bg-gradient-to-r from-gray-700 via-gray-600 to-gray-700"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group py-2">
                            <div class="flex-shrink-0">
                                <img
                                    src="{{ asset('images/industrial/logo.png') }}"
                                    alt="EMMS Logo"
                                    class="h-12 w-auto object-contain"
                                >
                            </div>
                        </a>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if(Route::has('login'))
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-150">
                                Login
                            </a>
                        @endif
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-700 rounded-lg shadow-md transition-colors duration-150 border border-gray-700">
                                Sign Up
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        {{-- Page content --}}
        <main class="flex-grow">
            @yield('content')
        </main>

        
     {{-- Footer --}}
    @include('layouts.footer')
    </div>
</body>
</html>