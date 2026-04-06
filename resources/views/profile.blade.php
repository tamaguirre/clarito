@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    {{-- Cabecera --}}
    <div class="flex items-center gap-4 mb-8">
        <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-lg font-semibold shrink-0"
            style="background-color: #0f1f3d;">
            {{ strtoupper(substr("Tamara Aguirre", 0, 2)) }}
        </div>
        <div>
            <p class="text-base font-semibold" style="color: #0f1f3d;">Tamara Aguirre</p>
            <p class="text-sm text-gray-500">tamara.henriquez1995@gmail.com</p>
        </div>
    </div>

    {{-- Datos personales --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-4">
        <h2 class="text-sm font-semibold mb-4" style="color: #0f1f3d;">Datos personales</h2>

        <form method="POST">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="name" class="block text-xs font-medium text-gray-500 mb-1.5">Nombre completo</label>
                    <input id="name" type="text" name="name" value="{{ old('name', 'Tamara Aguirre') }}" required
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-200 focus:border-cyan-400 transition
                            @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-xs font-medium text-gray-500 mb-1.5">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email', 'tamara.henriquez1995@gmail.com') }}" required
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-200 focus:border-cyan-400 transition
                            @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if (session('status') === 'profile-updated')
                <p class="text-xs text-green-600 bg-green-50 border border-green-200 rounded-lg px-3 py-2 mb-4">
                    Datos actualizados correctamente.
                </p>
            @endif

            <div class="flex justify-end gap-2">
                <button type="submit"
                    class="px-5 py-2 rounded-lg text-sm font-medium text-white transition hover:opacity-90"
                    style="background-color: #00bcd4;">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    {{-- Cambiar contraseña --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-4">
        <h2 class="text-sm font-semibold mb-4" style="color: #0f1f3d;">Cambiar contraseña</h2>

        <form method="POST">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div>
                    <label for="current_password" class="block text-xs font-medium text-gray-500 mb-1.5">Contraseña actual</label>
                    <div class="relative">
                        <input id="current_password" type="password" name="current_password"
                            class="w-full px-3 py-2 pr-20 text-sm rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-200 focus:border-cyan-400 transition
                                @error('current_password') border-red-400 @enderror"
                            placeholder="••••••••">
                        <button type="button"
                            data-password-toggle="current_password"
                            class="absolute inset-y-0 right-2 my-auto h-8 rounded-md px-2.5 text-xs font-medium text-cyan-700 hover:bg-cyan-50">
                            Mostrar
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-xs font-medium text-gray-500 mb-1.5">Nueva contraseña</label>
                    <div class="relative">
                        <input id="password" type="password" name="password"
                            class="w-full px-3 py-2 pr-20 text-sm rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-200 focus:border-cyan-400 transition
                                @error('password') border-red-400 @enderror"
                            placeholder="••••••••">
                        <button type="button"
                            data-password-toggle="password"
                            class="absolute inset-y-0 right-2 my-auto h-8 rounded-md px-2.5 text-xs font-medium text-cyan-700 hover:bg-cyan-50">
                            Mostrar
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-gray-500 mb-1.5">Confirmar</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            class="w-full px-3 py-2 pr-20 text-sm rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-200 focus:border-cyan-400 transition"
                            placeholder="••••••••">
                        <button type="button"
                            data-password-toggle="password_confirmation"
                            class="absolute inset-y-0 right-2 my-auto h-8 rounded-md px-2.5 text-xs font-medium text-cyan-700 hover:bg-cyan-50">
                            Mostrar
                        </button>
                    </div>
                </div>
            </div>

            @if (session('status') === 'password-updated')
                <p class="text-xs text-green-600 bg-green-50 border border-green-200 rounded-lg px-3 py-2 mb-4">
                    Contraseña actualizada correctamente.
                </p>
            @endif

            <div class="flex justify-end">
                <button type="submit"
                    class="px-5 py-2 rounded-lg text-sm font-medium text-white transition hover:opacity-90"
                    style="background-color: #00bcd4;">
                    Actualizar contraseña
                </button>
            </div>
        </form>
    </div>

    {{-- Suscripción --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="text-sm font-semibold mb-4" style="color: #0f1f3d;">Mi suscripción</h2>

        <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl px-5 py-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                    style="background-color: #e0f7fa;">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#00838f" stroke-width="2">
                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium" style="color: #0f1f3d;">Plan Gratuito</p>
                    <p class="text-xs text-gray-500">3 de 5 documentos usados este mes</p>
                </div>
            </div>
            <button class="px-4 py-2 rounded-lg text-xs font-semibold text-white transition hover:opacity-90"
                style="background-color: #0f1f3d;">
                Mejorar plan
            </button>
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-semibold" style="color: #0f1f3d;">3</p>
                <p class="text-xs text-gray-500 mt-0.5">docs este mes</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-semibold" style="color: #0f1f3d;">5</p>
                <p class="text-xs text-gray-500 mt-0.5">límite mensual</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-semibold" style="color: #0f1f3d;">12</p>
                <p class="text-xs text-gray-500 mt-0.5">docs en total</p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    (function () {
        const toggleButtons = Array.from(document.querySelectorAll('[data-password-toggle]'));

        toggleButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = button.getAttribute('data-password-toggle');

                if (!targetId) {
                    return;
                }

                const targetInput = document.getElementById(targetId);

                if (!(targetInput instanceof HTMLInputElement)) {
                    return;
                }

                const showing = targetInput.type === 'text';
                targetInput.type = showing ? 'password' : 'text';
                button.textContent = showing ? 'Mostrar' : 'Ocultar';
            });
        });
    })();
</script>
@endpush