@extends('layouts.app')

@section('title', 'Admin Empresas')

@push('scripts')
<script>
    (function () {
        const token = localStorage.getItem('access_token');
        const tokenType = localStorage.getItem('token_type') || 'Bearer';
        const rawUser = localStorage.getItem('auth_user');

        const statusBox = document.getElementById('admin-companies-status');
        const bodyBox = document.getElementById('admin-companies-body');
        const form = document.getElementById('admin-company-form');

        if (!token) {
            window.location.replace('/login');
            return;
        }

        if (!statusBox || !bodyBox || !form) {
            return;
        }

        try {
            const user = rawUser ? JSON.parse(rawUser) : null;

            if (user?.role?.name !== 'admin') {
                window.location.replace('/documents');
                return;
            }
        } catch {
            window.location.replace('/documents');
            return;
        }

        const setStatus = function (message, type) {
            const classes = {
                info: 'mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800',
                error: 'mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700',
                success: 'mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700',
            };

            statusBox.className = classes[type] || classes.info;
            statusBox.textContent = message;
        };

        const escapeHtml = function (value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        };

        const loadCompanies = async function () {
            setStatus('Cargando empresas...', 'info');

            try {
                const response = await fetch('/api/admin/companies', {
                    headers: {
                        Authorization: tokenType + ' ' + token,
                        Accept: 'application/json',
                    },
                });

                const payload = await response.json().catch(function () {
                    return {};
                });

                if (response.status === 401) {
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('token_type');
                    localStorage.removeItem('auth_user');
                    window.location.replace('/login');
                    return;
                }

                if (response.status === 403) {
                    window.location.replace('/documents');
                    return;
                }

                if (!response.ok) {
                    setStatus('No se pudieron cargar las empresas.', 'error');
                    return;
                }

                const companies = Array.isArray(payload?.data) ? payload.data : [];

                if (!companies.length) {
                    bodyBox.innerHTML = '<tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No hay empresas registradas.</td></tr>';
                } else {
                    bodyBox.innerHTML = companies.map(function (item) {
                        return '<tr class="border-t border-gray-100">'
                            + '<td class="px-4 py-3 text-sm text-gray-700">' + escapeHtml(item.name) + '</td>'
                            + '<td class="px-4 py-3 text-sm text-gray-600">' + escapeHtml(item.email || '-') + '</td>'
                            + '<td class="px-4 py-3 text-sm text-gray-600">' + escapeHtml(item.phone || '-') + '</td>'
                            + '<td class="px-4 py-3 text-sm text-gray-500">' + escapeHtml(item.created_at || '') + '</td>'
                            + '<td class="px-4 py-3 text-right">'
                            + '<button type="button" data-company-delete="' + item.id + '" class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100">Eliminar</button>'
                            + '</td>'
                            + '</tr>';
                    }).join('');
                }

                bodyBox.querySelectorAll('button[data-company-delete]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const companyId = button.getAttribute('data-company-delete');

                        if (!companyId) {
                            return;
                        }

                        void deleteCompany(companyId);
                    });
                });

                statusBox.className = 'hidden';
                statusBox.textContent = '';
            } catch {
                setStatus('Error de red al cargar empresas.', 'error');
            }
        };

        const deleteCompany = async function (companyId) {
            setStatus('Eliminando empresa...', 'info');

            const response = await fetch('/api/admin/companies/' + companyId, {
                method: 'DELETE',
                headers: {
                    Authorization: tokenType + ' ' + token,
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                setStatus('No se pudo eliminar la empresa.', 'error');
                return;
            }

            setStatus('Empresa eliminada correctamente.', 'success');
            void loadCompanies();
        };

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const formData = new FormData(form);

            setStatus('Creando empresa...', 'info');

            const response = await fetch('/api/admin/companies', {
                method: 'POST',
                headers: {
                    Authorization: tokenType + ' ' + token,
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    email: formData.get('email') || null,
                }),
            });

            if (response.status === 401) {
                localStorage.removeItem('access_token');
                localStorage.removeItem('token_type');
                localStorage.removeItem('auth_user');
                window.location.replace('/login');
                return;
            }

            if (response.status === 403) {
                window.location.replace('/documents');
                return;
            }

            if (!response.ok) {
                setStatus('No se pudo crear la empresa. Revisa los datos.', 'error');
                return;
            }

            form.reset();
            setStatus('Empresa creada correctamente.', 'success');
            void loadCompanies();
        });

        void loadCompanies();
    })();
</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div id="admin-companies-status" class="mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800">
        Cargando empresas...
    </div>

    <div class="mb-6">
        <h1 class="text-xl font-semibold" style="color:#0f1f3d;">Admin - Empresas</h1>
        <p class="text-sm text-gray-500">Crea y administra el catálogo de empresas.</p>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="mb-4 text-sm font-semibold" style="color:#0f1f3d;">Nueva empresa</h2>
        <form id="admin-company-form" class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <input type="text" name="name" required placeholder="Nombre"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
            <input type="email" name="email" required placeholder="Email"
                class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
            <button type="submit"
                class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                Crear empresa
            </button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Teléfono</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Creado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acción</th>
                </tr>
            </thead>
            <tbody id="admin-companies-body"></tbody>
        </table>
    </div>
</div>
@endsection
