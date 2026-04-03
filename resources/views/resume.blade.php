@extends('layouts.app')

@section('title', 'Resumen del documento')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">

        {{-- Vista previa del documento --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100 bg-gray-50">
                <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span class="text-xs font-medium text-gray-500 truncate">
                    {{-- {{ $documento->nombre }} --}}
                    contrato_arriendo.pdf
                </span>
            </div>

            {{-- Preview del PDF (placeholder) --}}
            <div class="p-4 min-h-80 space-y-2">
                @foreach(range(1, 12) as $i)
                    <div class="h-2.5 rounded-full bg-gray-100"
                        style="width: {{ [100, 85, 92, 78, 100, 88, 70, 100, 95, 60, 100, 82][$i-1] }}%">
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="flex justify-center items-center gap-1.5 px-4 py-3 border-t border-gray-100">
                <div class="w-2 h-2 rounded-full" style="background-color: #00bcd4;"></div>
                <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                <div class="w-2 h-2 rounded-full bg-gray-200"></div>
            </div>
        </div>

        {{-- Panel derecho --}}
        <div class="lg:col-span-3 flex flex-col gap-4">

            {{-- Resumen + audio --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold" style="color: #0f1f3d;">Resumen</h2>
                    <button class="flex items-center gap-1.5 text-xs font-medium rounded-full px-3 py-1.5 border transition hover:opacity-80"
                        style="background-color: #e0f7fa; border-color: #b2ebf2; color: #00838f;">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                            <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
                        </svg>
                        Escuchar
                    </button>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{-- {{ $resumen }} --}}
                    Este contrato de arriendo establece un acuerdo entre el propietario y el arrendatario por un período de 12 meses. El arriendo mensual es de $450.000 con vencimiento el día 5 de cada mes. El arrendatario debe mantener la propiedad en buen estado y no puede subarrendar sin autorización escrita.
                </p>
            </div>

            {{-- FAQ --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="text-sm font-semibold mb-3" style="color: #0f1f3d;">Preguntas frecuentes</h2>
                <div class="rounded-lg border border-gray-100 divide-y divide-gray-100 overflow-hidden">
                    @foreach([
                        '¿Cuánto debo pagar y cuándo?',
                        '¿Puedo salir antes del plazo?',
                        '¿Qué pasa si hay daños?'
                    ] as $pregunta)
                    <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm text-gray-700">{{ $pregunta }}</span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Tags --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="text-sm font-semibold mb-3" style="color: #0f1f3d;">Categorías</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Arriendo', 'Contrato', 'Legal', 'Vivienda', '12 meses'] as $tag)
                        <span class="text-xs font-medium px-3 py-1 rounded-full"
                            style="{{ $loop->index < 2 ? 'background:#e0f7fa; color:#00838f;' : 'background:#f1f5f9; color:#475569;' }}">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection