@extends('layouts.app')

@section('title', 'Resumen del documento')

@push('scripts')
<script>
    (function () {
        const token = localStorage.getItem('access_token');
        const tokenType = localStorage.getItem('token_type') || 'Bearer';
        const uploadToken = @json($uploadToken ?? null);

        if (!token) {
            window.location.replace('/login');
            return;
        }

        if (!uploadToken) {
            window.location.replace('/upload');
            return;
        }

        const statusBox = document.getElementById('resume-status');
        const titleBox = document.getElementById('resume-document-title');
        const previewBox = document.getElementById('resume-preview');
        const summaryBox = document.getElementById('resume-summary-text');
        const faqBox = document.getElementById('resume-faq-list');
        const tagsBox = document.getElementById('resume-tags');

        if (!statusBox || !titleBox || !previewBox || !summaryBox || !faqBox || !tagsBox) {
            return;
        }

        const setStatus = function (message, type) {
            const classes = {
                info: 'mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800',
                error: 'mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700',
            };

            statusBox.className = classes[type] || classes.info;
            statusBox.textContent = message;
        };

        const renderPreview = function (upload) {
            if (!upload.file_url || !upload.mime_type) {
                previewBox.innerHTML = '<p class="text-sm text-gray-500">No se pudo cargar la vista previa.</p>';
                return;
            }

            if (upload.mime_type === 'application/pdf') {
                previewBox.innerHTML = '<iframe src="' + upload.file_url + '" class="h-[520px] w-full rounded-lg border border-gray-100" title="Vista previa del PDF"></iframe>';
                return;
            }

            if (upload.mime_type.startsWith('image/')) {
                previewBox.innerHTML = '<img src="' + upload.file_url + '" alt="Documento cargado" class="mx-auto max-h-[520px] rounded-lg border border-gray-100 object-contain">';
                return;
            }

            previewBox.innerHTML = '<p class="text-sm text-gray-500">Tipo de archivo no soportado para vista previa.</p>';
        };

        const escapeHtml = function (value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        };

        const renderSummary = function (summary) {
            const sections = Array.isArray(summary?.resume) ? summary.resume : [];
            const faqItems = sections.flatMap(function (item) {
                return Array.isArray(item?.faq) ? item.faq : [];
            });

            if (!sections.length) {
                summaryBox.innerHTML = '<p class="text-sm text-gray-600 leading-relaxed">Resumen pendiente de generación.</p>';
            } else {
                summaryBox.innerHTML = sections.map(function (item) {
                    return '<article class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">'
                        + '<div class="mb-2 flex items-center gap-2">'
                        + '<span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-cyan-100 text-xs font-semibold text-cyan-700">' + escapeHtml(item.id) + '</span>'
                        + '<h3 class="text-sm font-semibold text-slate-800">' + escapeHtml(item.section) + '</h3>'
                        + '</div>'
                        + '<p class="text-sm leading-relaxed text-slate-600">' + escapeHtml(item.resume) + '</p>'
                        + '</article>';
                }).join('');
            }

            if (!faqItems.length) {
                faqBox.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">No hay preguntas frecuentes disponibles.</div>';
            } else {
                faqBox.innerHTML = faqItems.map(function (item) {
                    return '<details class="group px-4 py-3">'
                        + '<summary class="flex cursor-pointer list-none items-center justify-between gap-3 text-sm text-gray-700">'
                        + '<span>' + escapeHtml(item.question) + '</span>'
                        + '<svg class="h-4 w-4 shrink-0 text-gray-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">'
                        + '<polyline points="6 9 12 15 18 9"/>'
                        + '</svg>'
                        + '</summary>'
                        + '<p class="pt-3 text-sm leading-relaxed text-gray-500">' + escapeHtml(item.answer) + '</p>'
                        + '</details>';
                }).join('');
            }

        };

        const renderTags = function (tags) {
            if (!Array.isArray(tags) || !tags.length) {
                tagsBox.innerHTML = '<span class="text-xs text-gray-500">Sin categorías</span>';
                return;
            }

            tagsBox.innerHTML = tags.map(function (tag, index) {
                const style = index < 2
                    ? 'background:#e0f7fa; color:#00838f;'
                    : 'background:#f1f5f9; color:#475569;';

                return '<span class="text-xs font-medium px-3 py-1 rounded-full" style="' + style + '">' + escapeHtml(tag) + '</span>';
            }).join('');
        };

        const loadUpload = async function () {
            setStatus('Cargando documento...', 'info');

            try {
                const response = await fetch('/api/resumes/' + uploadToken, {
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
                    setStatus('No se pudo cargar el documento.', 'error');
                    return;
                }

                const payload = await response.json();
                const upload = payload?.data;

                if (!upload) {
                    setStatus('No se encontró información del documento.', 'error');
                    return;
                }

                titleBox.textContent = upload.original_name || 'Documento';
                renderPreview(upload);
                await loadResume();
                statusBox.className = 'hidden';
                statusBox.textContent = '';
            } catch {
                setStatus('No se pudo cargar el documento. Intenta nuevamente.', 'error');
            }
        };

        const loadResume = async function () {
            try {
                const response = await fetch('/api/resumes/' + uploadToken, {
                    headers: {
                        Authorization: tokenType + ' ' + token,
                        Accept: 'application/json',
                    },
                });

                if (response.status === 404) {
                    summaryBox.textContent = 'Resumen pendiente de generación. El documento ya fue cargado correctamente y en el siguiente paso procesaremos su contenido.';
                    return;
                }

                if (!response.ok) {
                    summaryBox.textContent = 'No se pudo cargar el resumen del documento.';
                    return;
                }

                const payload = await response.json();
                const summary = payload?.data?.summary_text;
                const tags = payload?.data?.tags;

                if (summary && typeof summary === 'object') {
                    renderSummary(summary);
                    renderTags(tags);
                    return;
                }

                summaryBox.innerHTML = '<p class="text-sm text-gray-600 leading-relaxed">Resumen pendiente de generación. El documento ya fue cargado correctamente y en el siguiente paso procesaremos su contenido.</p>';
                faqBox.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">No hay preguntas frecuentes disponibles.</div>';
                tagsBox.innerHTML = '<span class="text-xs text-gray-500">Sin categorías</span>';
            } catch {
                summaryBox.innerHTML = '<p class="text-sm text-gray-600 leading-relaxed">No se pudo cargar el resumen del documento.</p>';
                faqBox.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">No hay preguntas frecuentes disponibles.</div>';
                tagsBox.innerHTML = '<span class="text-xs text-gray-500">Sin categorías</span>';
            }
        };

        loadUpload();
    })();
</script>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div id="resume-status" class="mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800">
        Cargando documento...
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">

        {{-- Vista previa del documento --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100 bg-gray-50">
                <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span id="resume-document-title" class="text-xs font-medium text-gray-500 truncate">
                    Documento
                </span>
            </div>

            {{-- Preview del documento --}}
            <div id="resume-preview" class="p-4 min-h-80 space-y-2">
                <p class="text-sm text-gray-500">Preparando vista previa...</p>
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
                <div id="resume-summary-text" class="space-y-3">
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Resumen pendiente de generación.
                    </p>
                </div>
            </div>

            {{-- FAQ --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="text-sm font-semibold mb-3" style="color: #0f1f3d;">Preguntas frecuentes</h2>
                <div id="resume-faq-list" class="rounded-lg border border-gray-100 divide-y divide-gray-100 overflow-hidden">
                    <div class="px-4 py-3 text-sm text-gray-500">No hay preguntas frecuentes disponibles.</div>
                </div>
            </div>

            {{-- Tags --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="text-sm font-semibold mb-3" style="color: #0f1f3d;">Categorías</h2>
                <div id="resume-tags" class="flex flex-wrap gap-2">
                    <span class="text-xs text-gray-500">Sin categorías</span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection