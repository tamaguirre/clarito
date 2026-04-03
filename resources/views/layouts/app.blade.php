<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Clarito') }} — @yield('title', 'Inicio')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex flex-col" style="background-color: #f0f4f8;">

    {{-- Navbar --}}
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('upload') }}" class="flex items-center shrink-0">
                    <img src="{{ asset('img/logo.png') }}" alt="Clarito" class="h-18 w-auto">
                </a>

                {{-- Menú derecho --}}
                <div class="flex items-center gap-4">                
                    {{-- Avatar con dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold focus:outline-none"
                            style="background-color: #00bcd4; color: #0f1f3d;">
                            {{ strtoupper(substr("Tamara Aguirre", 0, 2)) }}
                        </button>

                        <div x-show="open" @click.outside="open = false"
                            class="absolute right-0 mt-2 w-44 bg-white rounded-xl border border-gray-100 py-1 z-50"
                            style="display: none;">
                            <a href="{{ route('upload') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                Mis documentos
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </nav>

    {{-- Contenido principal --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="py-5 border-t border-gray-200 bg-white">
        <p class="text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Clarito — Proyecto de Tesis
        </p>
    </footer>

</body>
</html>