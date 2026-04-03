<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro — Clarito</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/register.ts'])
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
            <div id="register-app"></div>
        </div>
    </div>

</body>
</html>