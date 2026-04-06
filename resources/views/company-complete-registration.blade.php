<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Completar registro empresa - Clarito</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 py-8">
<div class="mx-auto max-w-4xl px-4">
    <div class="mb-6 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800" id="status-box">
        Cargando invitacion...
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h1 class="mb-1 text-xl font-semibold" style="color:#0f1f3d;">Finalizar registro de empresa</h1>
        <p class="mb-6 text-sm text-gray-500">Completa esta informacion para activar tu cuenta empresarial.</p>

        <form id="company-complete-form" class="space-y-5" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-500">Empresa</label>
                    <input id="company-name" type="text" disabled class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-500">Email</label>
                    <input id="company-email" type="text" disabled class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label for="company_type_id" class="mb-1.5 block text-xs font-medium text-gray-500">Tipo de empresa</label>
                <select id="company_type_id" name="company_type_id" required
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100"></select>
            </div>

            <div>
                <label for="phone" class="mb-1.5 block text-xs font-medium text-gray-500">Teléfono de contacto</label>
                <input id="phone" name="phone" type="text" required
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
            </div>

            <div>
                <label for="short_description" class="mb-1.5 block text-xs font-medium text-gray-500">Descripcion breve</label>
                <textarea id="short_description" name="short_description" required rows="3"
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100"></textarea>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-xs font-medium text-gray-500">Contraseña</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required minlength="8"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 pr-20 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
                        <button type="button"
                            data-password-toggle="password"
                            class="absolute inset-y-0 right-2 my-auto h-8 rounded-md px-2.5 text-xs font-medium text-cyan-700 hover:bg-cyan-50">
                            Mostrar
                        </button>
                    </div>
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-xs font-medium text-gray-500">Confirmar contraseña</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 pr-20 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
                        <button type="button"
                            data-password-toggle="password_confirmation"
                            class="absolute inset-y-0 right-2 my-auto h-8 rounded-md px-2.5 text-xs font-medium text-cyan-700 hover:bg-cyan-50">
                            Mostrar
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <label for="logo" class="mb-1.5 block text-xs font-medium text-gray-500">Logo (opcional)</label>
                <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/webp"
                    class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-xs font-medium text-gray-500">Diccionario interno</label>
                    <button id="add-dictionary-row" type="button" class="rounded-lg border border-cyan-200 bg-cyan-50 px-2.5 py-1 text-xs font-medium text-cyan-700">Agregar termino</button>
                </div>
                <div id="dictionary-list" class="space-y-2"></div>
            </div>

            <div>
                <button type="submit" class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-medium text-white hover:opacity-90">Completar registro</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const token = @json($token);
        const statusBox = document.getElementById('status-box');
        const form = document.getElementById('company-complete-form');
        const companyNameInput = document.getElementById('company-name');
        const companyEmailInput = document.getElementById('company-email');
        const companyTypeSelect = document.getElementById('company_type_id');
        const dictionaryList = document.getElementById('dictionary-list');
        const addDictionaryRowButton = document.getElementById('add-dictionary-row');
        const toggleButtons = Array.from(document.querySelectorAll('[data-password-toggle]'));

        if (!statusBox || !form || !companyNameInput || !companyEmailInput || !companyTypeSelect || !dictionaryList || !addDictionaryRowButton) {
            return;
        }

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

        const setStatus = function (message, type) {
            const classes = {
                info: 'mb-6 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800',
                error: 'mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700',
                success: 'mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700',
            };

            statusBox.className = classes[type] || classes.info;
            statusBox.textContent = message;
        };

        const addDictionaryRow = function (word, definition) {
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 gap-2 rounded-lg border border-gray-200 bg-gray-50 p-3 md:grid-cols-[1fr_2fr_auto]';
            row.innerHTML = ''
                + '<input type="text" name="dictionary_word" placeholder="Palabra" required class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">'
                + '<input type="text" name="dictionary_definition" placeholder="Definicion" required class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">'
                + '<button type="button" class="remove-dictionary-row rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-medium text-red-600">Quitar</button>';

            const wordInput = row.querySelector('input[name="dictionary_word"]');
            const definitionInput = row.querySelector('input[name="dictionary_definition"]');
            const removeButton = row.querySelector('.remove-dictionary-row');

            if (wordInput instanceof HTMLInputElement && word) {
                wordInput.value = word;
            }

            if (definitionInput instanceof HTMLInputElement && definition) {
                definitionInput.value = definition;
            }

            if (removeButton instanceof HTMLButtonElement) {
                removeButton.addEventListener('click', function () {
                    row.remove();
                });
            }

            dictionaryList.appendChild(row);
        };

        addDictionaryRowButton.addEventListener('click', function () {
            addDictionaryRow('', '');
        });

        const loadInvitation = async function () {
            setStatus('Cargando invitacion...', 'info');

            const response = await fetch('/api/company-registrations/' + encodeURIComponent(token), {
                headers: {
                    Accept: 'application/json',
                },
            });

            const payload = await response.json().catch(function () {
                return {};
            });

            if (!response.ok) {
                setStatus(payload?.message || 'No se pudo cargar la invitacion.', 'error');
                return;
            }

            const company = payload?.data?.company?.data || payload?.data?.company;
            const types = payload?.data?.company_types?.data || payload?.data?.company_types || [];

            companyNameInput.value = company?.name || '';
            companyEmailInput.value = company?.email || '';

            companyTypeSelect.innerHTML = '<option value="">Selecciona tipo</option>' + types.map(function (item) {
                return '<option value="' + item.id + '">' + item.name + '</option>';
            }).join('');

            dictionaryList.innerHTML = '';
            addDictionaryRow('', '');

            setStatus('Invitacion validada. Completa los datos para activar tu empresa.', 'success');
        };

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const formData = new FormData();
            formData.append('company_type_id', companyTypeSelect.value);
            formData.append('phone', String(document.getElementById('phone')?.value || ''));
            formData.append('short_description', String(document.getElementById('short_description')?.value || ''));
            formData.append('password', String(document.getElementById('password')?.value || ''));
            formData.append('password_confirmation', String(document.getElementById('password_confirmation')?.value || ''));

            const logoInput = document.getElementById('logo');
            if (logoInput instanceof HTMLInputElement && logoInput.files && logoInput.files[0]) {
                formData.append('logo', logoInput.files[0]);
            }

            const words = Array.from(document.querySelectorAll('input[name="dictionary_word"]'));
            const defs = Array.from(document.querySelectorAll('input[name="dictionary_definition"]'));

            words.forEach(function (wordInput, index) {
                const defInput = defs[index];
                const wordValue = wordInput instanceof HTMLInputElement ? wordInput.value : '';
                const definitionValue = defInput instanceof HTMLInputElement ? defInput.value : '';
                formData.append('dictionary[' + index + '][word]', wordValue);
                formData.append('dictionary[' + index + '][definition]', definitionValue);
            });

            setStatus('Guardando datos...', 'info');

            const response = await fetch('/api/company-registrations/' + encodeURIComponent(token) + '/complete', {
                method: 'POST',
                body: formData,
                headers: {
                    Accept: 'application/json',
                },
            });

            const payload = await response.json().catch(function () {
                return {};
            });

            if (!response.ok) {
                setStatus(payload?.message || 'No se pudo completar el registro.', 'error');
                return;
            }

            setStatus('Registro completado correctamente. Seras redirigido al login en 3 segundos.', 'success');
            form.reset();
            dictionaryList.innerHTML = '';
            addDictionaryRow('', '');

            setTimeout(function () {
                window.location.replace('/login');
            }, 3000);
        });

        void loadInvitation();
    })();
</script>
</body>
</html>
