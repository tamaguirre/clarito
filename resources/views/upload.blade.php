@extends('layouts.app')

@section('title', 'Cargar documento')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    <h1 class="text-2xl font-semibold mb-1" style="color: #0f1f3d;">¿Qué vamos a aclarar hoy?</h1>
    <p class="text-sm text-gray-500 mb-8">Sube tu documento y deja que Clarito lo traduzca a lenguaje real.</p>

    <div id="drop-zone"
        class="border-2 border-dashed rounded-2xl bg-white text-center cursor-pointer transition-colors"
        style="border-color: #00bcd4; padding: 3.5rem 2rem;">

        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
            style="background-color: #e0f7fa;">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#00838f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
        </div>

        <p class="text-base font-medium mb-1" style="color: #0f1f3d;">Arrastra tu documento aquí</p>
        <p class="text-sm text-gray-400 mb-5">o haz clic para seleccionar un archivo</p>

        <div class="flex justify-center gap-2">
            <span class="text-xs px-3 py-1 rounded-full border border-gray-200 bg-gray-50 text-gray-500">PDF</span>
            <span class="text-xs px-3 py-1 rounded-full border border-gray-200 bg-gray-50 text-gray-500">JPG</span>
            <span class="text-xs px-3 py-1 rounded-full border border-gray-200 bg-gray-50 text-gray-500">PNG</span>
        </div>
    </div>

    <p class="text-xs text-center text-gray-400 mt-3">Tamaño máximo: 10 MB</p>

</div>
@endsection