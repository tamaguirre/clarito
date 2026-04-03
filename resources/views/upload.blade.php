@extends('layouts.app')

@section('title', 'Cargar documento')

@push('scripts')
<script>
    (function () {
        const token = localStorage.getItem('access_token');
        const tokenType = localStorage.getItem('token_type') || 'Bearer';
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('upload-file-input');
        const statusBox = document.getElementById('upload-status');
        const fileNameBox = document.getElementById('upload-file-name');

        if (!token) {
            window.location.replace('/login');
            return;
        }

        if (!dropZone || !fileInput || !statusBox || !fileNameBox) {
            return;
        }

        const allowedMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        const resetStatus = function () {
            statusBox.className = 'hidden mt-4 rounded-lg px-4 py-3 text-sm';
            statusBox.textContent = '';
        };

        const setStatus = function (type, message) {
            const classes = {
                success: 'mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700',
                error: 'mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700',
                info: 'mt-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800',
            };

            statusBox.className = classes[type] || classes.info;
            statusBox.textContent = message;
        };

        const validateFile = function (file) {
            if (!allowedMimeTypes.includes(file.type)) {
                setStatus('error', 'Solo se permiten PDF e imágenes (JPG, PNG, WEBP).');
                return false;
            }

            if (file.size > 10 * 1024 * 1024) {
                setStatus('error', 'El archivo supera el tamaño máximo de 10 MB.');
                return false;
            }

            return true;
        };

        const uploadFile = async function (file) {
            resetStatus();

            if (!validateFile(file)) {
                return;
            }

            fileNameBox.textContent = 'Archivo seleccionado: ' + file.name;
            setStatus('info', 'Cargando archivo...');

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch('/api/uploads', {
                    method: 'POST',
                    headers: {
                        Authorization: tokenType + ' ' + token,
                        Accept: 'application/json',
                    },
                    body: formData,
                });

                const payload = await response.json().catch(() => ({}));

                if (response.status === 401) {
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('token_type');
                    localStorage.removeItem('auth_user');
                    window.location.replace('/login');
                    return;
                }

                if (response.status === 422) {
                    const errorMessage = payload?.errors?.file?.[0] || 'Archivo inválido.';
                    setStatus('error', errorMessage);
                    return;
                }

                if (!response.ok) {
                    setStatus('error', 'No se pudo cargar el archivo. Intenta nuevamente.');
                    return;
                }

                setStatus('success', 'Archivo cargado correctamente.');

                const tokenFromResponse = payload?.data?.token;

                if (tokenFromResponse) {
                    window.location.replace('/resume/' + tokenFromResponse);
                }
            } catch {
                setStatus('error', 'No se pudo cargar el archivo. Revisa tu conexión e intenta otra vez.');
            }
        };

        dropZone.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function (event) {
            const target = event.target;

            if (!(target instanceof HTMLInputElement) || !target.files || !target.files[0]) {
                return;
            }

            uploadFile(target.files[0]);
            target.value = '';
        });

        dropZone.addEventListener('dragover', function (event) {
            event.preventDefault();
            dropZone.classList.add('bg-cyan-50');
        });

        dropZone.addEventListener('dragleave', function () {
            dropZone.classList.remove('bg-cyan-50');
        });

        dropZone.addEventListener('drop', function (event) {
            event.preventDefault();
            dropZone.classList.remove('bg-cyan-50');

            const files = event.dataTransfer?.files;

            if (!files || !files[0]) {
                return;
            }

            uploadFile(files[0]);
        });
    })();
</script>
@endpush

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    <h1 class="text-2xl font-semibold mb-1" style="color: #0f1f3d;">¿Qué vamos a aclarar hoy?</h1>
    <p class="text-sm text-gray-500 mb-8">Sube tu documento y deja que Clarito lo traduzca a lenguaje real.</p>

    <div id="drop-zone"
        class="border-2 border-dashed rounded-2xl bg-white text-center cursor-pointer transition-colors"
        style="border-color: #00bcd4; padding: 3.5rem 2rem;">

        <input id="upload-file-input" type="file" accept="application/pdf,image/jpeg,image/png,image/webp" class="hidden">

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
    <p id="upload-file-name" class="mt-4 text-sm text-gray-500 text-center"></p>
    <div id="upload-status" class="hidden mt-4 rounded-lg px-4 py-3 text-sm"></div>

</div>
@endsection