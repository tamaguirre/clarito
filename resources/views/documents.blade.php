@extends('layouts.app')

@section('title', 'Mis documentos')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">

    {{-- Cabecera --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-xl font-semibold" style="color: #0f1f3d;">Mis documentos</h1>
            <p class="text-sm text-gray-500 mt-0.5">12 documentos procesados</p>
        </div>
        <a href="{{ route('upload') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-white rounded-lg px-4 py-2 transition hover:opacity-90"
            style="background-color: #00bcd4;">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nuevo documento
        </a>
    </div>

    {{-- Grid de cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Card 1 --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col">
            <div class="p-4 flex-1">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #fef2f2;">
                        <span class="text-xs font-bold" style="color: #e53e3e;">PDF</span>
                    </div>
                    <span class="text-xs text-gray-400">01 abr 2025</span>
                </div>
                <p class="text-sm font-medium mb-1 leading-snug" style="color: #0f1f3d;">Contrato de arriendo</p>
                <p class="text-xs text-gray-400 mb-3 truncate">contrato_arriendo.pdf</p>
                <div class="flex flex-wrap gap-1">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background-color: #e0f7fa; color: #00838f;">Arriendo</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background-color: #e0f7fa; color: #00838f;">Legal</span>
                </div>
            </div>
            <div class="border-t border-gray-100 grid grid-cols-2">
                <a href="#" class="flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium border-r border-gray-100 hover:bg-gray-50 transition" style="color: #00838f;">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Ver resumen
                </a>
                <button class="flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium hover:bg-red-50 transition" style="color: #ef4444;">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                    Eliminar
                </button>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col">
            <div class="p-4 flex-1">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #fef2f2;">
                        <span class="text-xs font-bold" style="color: #e53e3e;">PDF</span>
                    </div>
                    <span class="text-xs text-gray-400">28 mar 2025</span>
                </div>
                <p class="text-sm font-medium mb-1 leading-snug" style="color: #0f1f3d;">Informe médico anual</p>
                <p class="text-xs text-gray-400 mb-3 truncate">informe_medico.pdf</p>
                <div class="flex flex-wrap gap-1">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background-color: #e0f7fa; color: #00838f;">Salud</span>
                </div>
            </div>
            <div class="border-t border-gray-100 grid grid-cols-2">
                <a href="#" class="flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium border-r border-gray-100 hover:bg-gray-50 transition" style="color: #00838f;">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Ver resumen
                </a>
                <button class="flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium hover:bg-red-50 transition" style="color: #ef4444;">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                    Eliminar
                </button>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col">
            <div class="p-4 flex-1">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #f0f9ff;">
                        <span class="text-xs font-bold" style="color: #3182ce;">IMG</span>
                    </div>
                    <span class="text-xs text-gray-400">20 mar 2025</span>
                </div>
                <p class="text-sm font-medium mb-1 leading-snug" style="color: #0f1f3d;">Contrato de trabajo</p>
                <p class="text-xs text-gray-400 mb-3 truncate">contrato_trabajo.jpg</p>
                <div class="flex flex-wrap gap-1">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background-color: #e0f7fa; color: #00838f;">Laboral</span>
                </div>
            </div>
            <div class="border-t border-gray-100 grid grid-cols-2">
                <a href="#" class="flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium border-r border-gray-100 hover:bg-gray-50 transition" style="color: #00838f;">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Ver resumen
                </a>
                <button class="flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium hover:bg-red-50 transition" style="color: #ef4444;">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                    Eliminar
                </button>
            </div>
        </div>

        {{-- Card 4 --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col">
            <div class="p-4 flex-1">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #fef2f2;">
                        <span class="text-xs font-bold" style="color: #e53e3e;">PDF</span>
                    </div>
                    <span class="text-xs text-gray-400">15 mar 2025</span>
                </div>
                <p class="text-sm font-medium mb-1 leading-snug" style="color: #0f1f3d;">Escritura de propiedad</p>
                <p class="text-xs text-gray-400 mb-3 truncate">escritura_propiedad.pdf</p>
                <div class="flex flex-wrap gap-1">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background-color: #e0f7fa; color: #00838f;">Legal</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background-color: #e0f7fa; color: #00838f;">Vivienda</span>
                </div>
            </div>
            <div class="border-t border-gray-100 grid grid-cols-2">
                <a href="#" class="flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium border-r border-gray-100 hover:bg-gray-50 transition" style="color: #00838f;">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Ver resumen
                </a>
                <button class="flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium hover:bg-red-50 transition" style="color: #ef4444;">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                    Eliminar
                </button>
            </div>
        </div>

    </div>
</div>
@endsection