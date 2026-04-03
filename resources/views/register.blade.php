<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro — Clarito</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex" style="background-color: #f0f4f8;">

    {{-- Panel izquierdo decorativo --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12" style="background-color: #0f1f3d;">
        <div>
        </div>
        <div>
            <h2 class="text-white text-3xl font-semibold leading-snug mb-4">
                Entiende cualquier documento,<br>sin ser experto.
            </h2>
            <p class="text-blue-200 text-sm leading-relaxed">
                Sube contratos, informes o textos técnicos y Clarito te explica lo que realmente dice, en palabras simples.
            </p>
        </div>
        <p class="text-blue-300 text-xs">&copy; {{ date('Y') }} Clarito — Proyecto de Tesis</p>
    </div>

    {{-- Panel derecho: formulario --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">

            {{-- Logo mobile --}}
            <div class="flex justify-center mb-6">
                <img src="{{ asset('img/logo.png') }}" alt="Clarito" class="h-28 w-auto">
            </div>

            <h1 class="text-2xl font-semibold mb-1" style="color: #0f1f3d;">Crear una cuenta</h1>
            <p class="text-sm text-gray-500 mb-8">Empieza a entender tus documentos en segundos</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Nombre --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nombre completo</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Juan Pérez"
                        class="w-full px-4 py-2.5 text-sm rounded-lg border bg-white focus:outline-none focus:ring-2 transition
                            {{ $errors->has('name') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:ring-cyan-200 focus:border-cyan-400' }}">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        placeholder="juan@correo.com"
                        class="w-full px-4 py-2.5 text-sm rounded-lg border bg-white focus:outline-none focus:ring-2 transition
                            {{ $errors->has('email') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:ring-cyan-200 focus:border-cyan-400' }}">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                    <input id="password" type="password" name="password" required
                        placeholder="Mínimo 8 caracteres"
                        class="w-full px-4 py-2.5 text-sm rounded-lg border bg-white focus:outline-none focus:ring-2 transition
                            {{ $errors->has('password') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:ring-cyan-200 focus:border-cyan-400' }}">
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmar contraseña --}}
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        placeholder="Repite tu contraseña"
                        class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-200 focus:border-cyan-400 transition">
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-2.5 rounded-lg text-sm font-semibold text-white transition hover:opacity-90 active:scale-95"
                    style="background-color: #00bcd4;">
                    Crear cuenta
                </button>

                <p class="text-center text-sm text-gray-500 mt-6">
                    ¿Ya tienes cuenta?
                    <a href="{{ route('login') }}" class="font-medium hover:underline" style="color: #00bcd4;">Inicia sesión</a>
                </p>
            </form>
        </div>
    </div>

</body>
</html>