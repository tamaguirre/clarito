@extends('layouts.app')

@section('title', 'Mis documentos')

@push('scripts')
<script>
    (function () {
        const token = localStorage.getItem('access_token');
        const tokenType = localStorage.getItem('token_type') || 'Bearer';
        const list = document.getElementById('documents-list');
        const count = document.getElementById('documents-count');
        const status = document.getElementById('documents-status');

        if (!token) {
            window.location.replace('/login');
            return;
        }

        if (!list || !count || !status) {
            return;
        }

        const escapeHtml = function (value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        };

        const formatDate = function (value) {
            if (!value) {
                return 'Sin fecha';
            }

            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return 'Sin fecha';
            }

            return date.toLocaleDateString('es-CL', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            });
        };

        const detectType = function (mimeType) {
            if (mimeType === 'application/pdf') {
                return {
                    label: 'PDF',
                    bg: '#fef2f2',
                    color: '#e53e3e',
                };
            }

            return {
                label: 'IMG',
                bg: '#f0f9ff',
                color: '#3182ce',
            };
        };

        const renderCard = function (doc) {
            const type = detectType(doc.mime_type);
            const tags = Array.isArray(doc.tags) ? doc.tags : [];
            const tagsHtml = tags.length
                ? tags.map(function (tag) {
                    return '<span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background-color: #e0f7fa; color: #00838f;">'
                        + escapeHtml(tag)
                        + '</span>';
                }).join('')
                : '<span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background-color: #f1f5f9; color: #64748b;">Sin tags</span>';

            return '<div class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col">'
                + '<div class="p-4 flex-1">'
                + '<div class="flex items-start justify-between mb-3">'
                + '<div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: ' + type.bg + ';">'
                + '<span class="text-xs font-bold" style="color: ' + type.color + ';">' + type.label + '</span>'
                + '</div>'
                + '<span class="text-xs text-gray-400">' + escapeHtml(formatDate(doc.created_at)) + '</span>'
                + '</div>'
                + '<p class="text-sm font-medium mb-1 leading-snug" style="color: #0f1f3d;">' + escapeHtml(doc.original_name || 'Documento') + '</p>'
                + '<p class="text-xs text-gray-400 mb-3 truncate">' + escapeHtml(doc.token || '') + '</p>'
                + '<div class="flex flex-wrap gap-1">' + tagsHtml + '</div>'
                + '</div>'
                + '<div class="border-t border-gray-100 grid grid-cols-1">'
                + '<a href="/resume/' + encodeURIComponent(doc.token) + '" class="flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium hover:bg-gray-50 transition" style="color: #00838f;">'
                + '<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>'
                + 'Ver resumen'
                + '</a>'
                + '</div>'
                + '</div>';
        };

        const renderEmpty = function () {
            list.innerHTML = '<div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center">'
                + '<p class="text-sm text-gray-500">Todavía no tienes documentos procesados.</p>'
                + '</div>';
        };

        const loadDocuments = async function () {
            status.className = 'mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800';
            status.textContent = 'Cargando documentos...';

            try {
                const response = await fetch('/api/resumes', {
                    headers: {
                        Authorization: tokenType + ' ' + token,
                        Accept: 'application/json',
                    },
                });

                if (response.status === 401) {
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('token_type');
                    localStorage.removeItem('auth_user');
                    window.location.replace('/login');
                    return;
                }

                if (!response.ok) {
                    status.className = 'mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700';
                    status.textContent = 'No se pudieron cargar los documentos.';
                    return;
                }

                const payload = await response.json();
                const documents = Array.isArray(payload?.data) ? payload.data : [];

                count.textContent = documents.length + ' documentos procesados';

                if (!documents.length) {
                    renderEmpty();
                } else {
                    list.innerHTML = documents.map(renderCard).join('');
                }

                status.className = 'hidden';
                status.textContent = '';
            } catch {
                status.className = 'mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700';
                status.textContent = 'No se pudieron cargar los documentos. Intenta nuevamente.';
            }
        };

        loadDocuments();
    })();
</script>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">

    <div id="documents-status" class="mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800">
        Cargando documentos...
    </div>

    {{-- Cabecera --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-xl font-semibold" style="color: #0f1f3d;">Mis documentos</h1>
            <p id="documents-count" class="text-sm text-gray-500 mt-0.5">0 documentos procesados</p>
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
    <div id="documents-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"></div>
</div>
@endsection