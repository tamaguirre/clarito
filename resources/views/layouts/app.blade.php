<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Clarito') }} — @yield('title', 'Inicio')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
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
                <a href="{{ route('documents') }}" class="flex items-center shrink-0">
                    <img src="{{ asset('img/logo.png') }}" alt="Clarito" class="h-18 w-auto">
                </a>

                {{-- Menú derecho --}}
                <div class="flex items-center gap-3">
                    <p id="menu-user-name" class="hidden sm:block text-sm font-medium text-gray-700">
                        Invitado
                    </p>

                    {{-- Avatar con dropdown --}}
                    <div id="menu-dropdown-root" class="relative">
                        <button id="menu-dropdown-toggle" type="button"
                            class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold focus:outline-none"
                            style="background-color: #00bcd4; color: #0f1f3d;">
                            <span id="menu-user-initials">US</span>
                        </button>

                        <div id="menu-dropdown-panel"
                            class="hidden absolute right-0 mt-2 w-52 bg-white rounded-xl border border-gray-100 py-1 z-50"
                            style="display: none;">
                            <a href="{{ route('documents') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                Mis documentos
                            </a>
                            <a href="{{ route('upload') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                Nuevo Documento
                            </a>
                            <div id="menu-admin-links" class="hidden">
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('admin.users') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Admin Usuarios
                                </a>
                                <a href="{{ route('admin.companies') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Admin Empresas
                                </a>
                            </div>
                            <div class="border-t border-gray-100 my-1"></div>
                            <button id="menu-logout-button" type="button"
                                class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50">
                                Cerrar sesión
                            </button>
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

    <script>
        (function () {
            const nameElement = document.getElementById('menu-user-name');
            const initialsElement = document.getElementById('menu-user-initials');
            const logoutButton = document.getElementById('menu-logout-button');
            const dropdownRoot = document.getElementById('menu-dropdown-root');
            const dropdownToggle = document.getElementById('menu-dropdown-toggle');
            const dropdownPanel = document.getElementById('menu-dropdown-panel');
            const adminLinks = document.getElementById('menu-admin-links');

            if (!nameElement || !initialsElement) {
                return;
            }

            try {
                const rawUser = localStorage.getItem('auth_user');

                if (!rawUser) {
                    return;
                }

                const user = JSON.parse(rawUser);
                const fullName = typeof user?.name === 'string' ? user.name.trim() : '';
                const roleName = typeof user?.role?.name === 'string' ? user.role.name : '';

                if (!fullName) {
                    return;
                }

                nameElement.textContent = fullName;

                const initials = fullName
                    .split(/\s+/)
                    .filter(Boolean)
                    .slice(0, 2)
                    .map((part) => part[0].toUpperCase())
                    .join('');

                initialsElement.textContent = initials || 'US';

                if (adminLinks && roleName === 'admin') {
                    adminLinks.classList.remove('hidden');
                }
            } catch {
                // Keep fallback values when local storage data is not valid.
            }

            if (logoutButton) {
                logoutButton.addEventListener('click', function () {
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('token_type');
                    localStorage.removeItem('auth_user');

                    window.location.replace('/login');
                });
            }

            if (dropdownRoot && dropdownToggle && dropdownPanel) {
                const closeDropdown = function () {
                    dropdownPanel.classList.add('hidden');
                    dropdownPanel.style.display = 'none';
                };

                dropdownToggle.addEventListener('click', function (event) {
                    event.stopPropagation();

                    const isOpen = !dropdownPanel.classList.contains('hidden');

                    if (isOpen) {
                        closeDropdown();
                        return;
                    }

                    dropdownPanel.classList.remove('hidden');
                    dropdownPanel.style.display = 'block';
                });

                document.addEventListener('click', function (event) {
                    if (!(event.target instanceof Node)) {
                        return;
                    }

                    if (!dropdownRoot.contains(event.target)) {
                        closeDropdown();
                    }
                });
            }
        })();
    </script>

    @stack('scripts')

</body>
</html>