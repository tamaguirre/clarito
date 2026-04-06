@extends('layouts.app')

@section('title', 'Admin Usuarios')

@push('scripts')
<script>
    (function () {
        const token = localStorage.getItem('access_token');
        const tokenType = localStorage.getItem('token_type') || 'Bearer';
        const rawUser = localStorage.getItem('auth_user');

        const statusBox = document.getElementById('admin-users-status');
        const tableBody = document.getElementById('admin-users-body');

        if (!token) {
            window.location.replace('/login');
            return;
        }

        if (!statusBox || !tableBody) {
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

        const fetchJson = async function (url) {
            const response = await fetch(url, {
                headers: {
                    Authorization: tokenType + ' ' + token,
                    Accept: 'application/json',
                },
            });

            const payload = await response.json().catch(function () {
                return {};
            });

            return { response, payload };
        };

        const loadUsers = async function () {
            setStatus('Cargando catálogo de usuarios...', 'info');

            try {
                const usersResult = await fetchJson('/api/admin/users');

                if (usersResult.response.status === 401) {
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('token_type');
                    localStorage.removeItem('auth_user');
                    window.location.replace('/login');
                    return;
                }

                if (usersResult.response.status === 403) {
                    window.location.replace('/documents');
                    return;
                }

                if (!usersResult.response.ok) {
                    setStatus('No se pudo cargar el catálogo de usuarios.', 'error');
                    return;
                }

                const users = Array.isArray(usersResult.payload?.data) ? usersResult.payload.data : [];

                if (!users.length) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No hay usuarios para mostrar.</td></tr>';
                    statusBox.className = 'hidden';
                    statusBox.textContent = '';
                    return;
                }

                tableBody.innerHTML = users.map(function (item) {
                    return '<tr class="border-t border-gray-100">'
                        + '<td class="px-4 py-3 text-sm text-gray-700">' + escapeHtml(item.name) + '</td>'
                        + '<td class="px-4 py-3 text-sm text-gray-600">' + escapeHtml(item.email) + '</td>'
                        + '<td class="px-4 py-3 text-sm text-gray-600">' + escapeHtml(item?.role?.name || 'Sin rol') + '</td>'
                        + '<td class="px-4 py-3 text-sm text-gray-600">' + escapeHtml(item?.company?.name || 'Sin empresa') + '</td>'
                        + '<td class="px-4 py-3 text-sm text-gray-500">' + escapeHtml(item.created_at ?? '') + '</td>'
                        + '</tr>';
                }).join('');

                statusBox.className = 'hidden';
                statusBox.textContent = '';
            } catch {
                setStatus('Error de red al cargar usuarios.', 'error');
            }
        };

        void loadUsers();
    })();
</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div id="admin-users-status" class="mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800">
        Cargando catálogo de usuarios...
    </div>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold" style="color:#0f1f3d;">Admin - Usuarios</h1>
            <p class="text-sm text-gray-500">Listado de usuarios (solo lectura).</p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Rol</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Empresa</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Creado</th>
                </tr>
            </thead>
            <tbody id="admin-users-body"></tbody>
        </table>
    </div>
</div>
@endsection
