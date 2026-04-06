@extends('layouts.app')

@section('title', 'Empresa')

@push('scripts')
<script>
	(function () {
		const token = localStorage.getItem('access_token');
		const tokenType = localStorage.getItem('token_type') || 'Bearer';
		const rawUser = localStorage.getItem('auth_user');
		const selectedEnvironmentKey = 'company_selected_environment_id';

		const statusBox = document.getElementById('company-home-status');
		const envSelect = document.getElementById('company-environment-select');
		const settingsForm = document.getElementById('company-settings-form');
		const webhooksBody = document.getElementById('company-webhooks-body');
		const webhookForm = document.getElementById('company-webhook-form');

		const actionTypeSelect = document.getElementById('action_type_id');
		const allowMultipleInput = document.getElementById('allow_multiple_confirmation');
		const linkExpirationInput = document.getElementById('link_expiration_hours');
		const accessMethodSelect = document.getElementById('access_method_id');
		const aiToneSelect = document.getElementById('ai_tone_id');
		const returnButtonTextInput = document.getElementById('return_button_text');
		const returnButtonUrlInput = document.getElementById('return_button_url');
		const allowCalendarInput = document.getElementById('allow_calendar_dates');
		const sendSummaryInput = document.getElementById('send_summary_pdf_by_email');

		const webhookNameInput = document.getElementById('webhook_name');
		const webhookUrlInput = document.getElementById('webhook_url');
		const webhookSecretInput = document.getElementById('webhook_secret');
		const webhookEventsInput = document.getElementById('webhook_events');
		const webhookActiveInput = document.getElementById('webhook_is_active');

		let catalogs = {
			environments: [],
			action_types: [],
			access_methods: [],
			ai_tones: [],
		};

		let currentEnvironmentId = null;

		if (!token) {
			window.location.replace('/login');
			return;
		}

		try {
			const user = rawUser ? JSON.parse(rawUser) : null;

			if (user?.role?.name !== 'company') {
				window.location.replace('/documents');
				return;
			}
		} catch {
			window.location.replace('/login');
			return;
		}

		if (!statusBox || !envSelect || !settingsForm || !webhooksBody || !webhookForm) {
			return;
		}

		const authorizedHeaders = function (withJsonContentType) {
			const headers = {
				Authorization: tokenType + ' ' + token,
				Accept: 'application/json',
			};

			if (withJsonContentType) {
				headers['Content-Type'] = 'application/json';
			}

			return headers;
		};

		const clearSessionAndRedirectToLogin = function () {
			localStorage.removeItem('access_token');
			localStorage.removeItem('token_type');
			localStorage.removeItem('auth_user');
			window.location.replace('/login');
		};

		const setStatus = function (message, type) {
			const classes = {
				info: 'mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800',
				error: 'mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700',
				success: 'mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700',
			};

			statusBox.className = classes[type] || classes.info;
			statusBox.textContent = message;
		};

		const hideStatus = function () {
			statusBox.className = 'hidden';
			statusBox.textContent = '';
		};

		const escapeHtml = function (value) {
			return String(value ?? '')
				.replaceAll('&', '&amp;')
				.replaceAll('<', '&lt;')
				.replaceAll('>', '&gt;')
				.replaceAll('"', '&quot;')
				.replaceAll("'", '&#039;');
		};

		const fillSelectOptions = function (selectElement, items, placeholderLabel) {
			const options = ['<option value="">' + escapeHtml(placeholderLabel) + '</option>'];

			items.forEach(function (item) {
				options.push('<option value="' + escapeHtml(item.id) + '">' + escapeHtml(item.name) + '</option>');
			});

			selectElement.innerHTML = options.join('');
		};

		const toggleAllowMultipleAvailability = function () {
			const actionTypeId = Number(actionTypeSelect.value || 0);
			const selectedAction = catalogs.action_types.find(function (item) {
				return Number(item.id) === actionTypeId;
			});

			const canUseMultiple = selectedAction?.name === 'confirmacion';

			allowMultipleInput.disabled = !canUseMultiple;

			if (!canUseMultiple) {
				allowMultipleInput.checked = false;
			}
		};

		const getCurrentEnvironmentId = function () {
			if (!currentEnvironmentId) {
				return null;
			}

			return String(currentEnvironmentId);
		};

		const loadCatalogs = async function () {
			const response = await fetch('/api/company/catalogs', {
				headers: authorizedHeaders(false),
			});

			if (response.status === 401) {
				clearSessionAndRedirectToLogin();
				return false;
			}

			if (response.status === 403) {
				window.location.replace('/documents');
				return false;
			}

			if (!response.ok) {
				setStatus('No se pudieron cargar los catalogos.', 'error');
				return false;
			}

			const payload = await response.json().catch(function () {
				return {};
			});

			catalogs = {
				environments: Array.isArray(payload?.data?.environments) ? payload.data.environments : [],
				action_types: Array.isArray(payload?.data?.action_types) ? payload.data.action_types : [],
				access_methods: Array.isArray(payload?.data?.access_methods) ? payload.data.access_methods : [],
				ai_tones: Array.isArray(payload?.data?.ai_tones) ? payload.data.ai_tones : [],
			};

			fillSelectOptions(envSelect, catalogs.environments, 'Selecciona ambiente');
			fillSelectOptions(actionTypeSelect, catalogs.action_types, 'Selecciona tipo de accion');
			fillSelectOptions(accessMethodSelect, catalogs.access_methods, 'Selecciona metodo de acceso');
			fillSelectOptions(aiToneSelect, catalogs.ai_tones, 'Selecciona tono IA');

			const savedEnvironment = localStorage.getItem(selectedEnvironmentKey);
			const hasSavedEnvironment = catalogs.environments.some(function (item) {
				return String(item.id) === String(savedEnvironment);
			});

			const defaultEnvironment = hasSavedEnvironment
				? String(savedEnvironment)
				: String(catalogs.environments[0]?.id || '');

			if (!defaultEnvironment) {
				setStatus('No hay ambientes disponibles.', 'error');
				return false;
			}

			envSelect.value = defaultEnvironment;
			currentEnvironmentId = Number(defaultEnvironment);
			localStorage.setItem(selectedEnvironmentKey, defaultEnvironment);
			toggleAllowMultipleAvailability();

			return true;
		};

		const applySettingsValues = function (settings) {
			const actionTypeId = settings?.action_type_id;
			const accessMethodId = settings?.access_method_id;
			const aiToneId = settings?.ai_tone_id;
			const returnButton = settings?.return_button || {};

			actionTypeSelect.value = actionTypeId ? String(actionTypeId) : '';
			accessMethodSelect.value = accessMethodId ? String(accessMethodId) : '';
			aiToneSelect.value = aiToneId ? String(aiToneId) : '';

			allowMultipleInput.checked = Boolean(settings?.allow_multiple_confirmation);
			linkExpirationInput.value = Number(settings?.link_expiration_hours || 24);
			returnButtonTextInput.value = String(returnButton?.text || 'Volver al inicio');
			returnButtonUrlInput.value = String(returnButton?.url || window.location.origin);
			allowCalendarInput.checked = Boolean(settings?.allow_calendar_dates);
			sendSummaryInput.checked = Boolean(settings?.send_summary_pdf_by_email);

			toggleAllowMultipleAvailability();
		};

		const loadSettings = async function () {
			const envId = getCurrentEnvironmentId();

			if (!envId) {
				return;
			}

			const response = await fetch('/api/company/configs/' + envId, {
				headers: authorizedHeaders(false),
			});

			if (response.status === 401) {
				clearSessionAndRedirectToLogin();
				return;
			}

			if (response.status === 403) {
				window.location.replace('/documents');
				return;
			}

			if (!response.ok) {
				setStatus('No se pudieron cargar las configuraciones del ambiente.', 'error');
				return;
			}

			const payload = await response.json().catch(function () {
				return {};
			});

			applySettingsValues(payload?.data?.settings || {});
		};

		const renderWebhooks = function (webhooks) {
			if (!Array.isArray(webhooks) || !webhooks.length) {
				webhooksBody.innerHTML = '<tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No hay webhooks para este ambiente.</td></tr>';
				return;
			}

			webhooksBody.innerHTML = webhooks.map(function (item) {
				const events = Array.isArray(item.events) && item.events.length
					? item.events.join(', ')
					: '-';

				return '<tr class="border-t border-gray-100">'
					+ '<td class="px-4 py-3 text-sm text-gray-700">' + escapeHtml(item.name) + '</td>'
					+ '<td class="px-4 py-3 text-sm text-gray-600">' + escapeHtml(item.url) + '</td>'
					+ '<td class="px-4 py-3 text-sm text-gray-600">' + escapeHtml(events) + '</td>'
					+ '<td class="px-4 py-3 text-sm ' + (item.is_active ? 'text-green-700' : 'text-gray-500') + '">' + (item.is_active ? 'Activo' : 'Inactivo') + '</td>'
					+ '<td class="px-4 py-3 text-right">'
					+ '<button type="button" data-webhook-delete="' + item.id + '" class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100">Eliminar</button>'
					+ '</td>'
					+ '</tr>';
			}).join('');

			webhooksBody.querySelectorAll('button[data-webhook-delete]').forEach(function (button) {
				button.addEventListener('click', function () {
					const webhookId = button.getAttribute('data-webhook-delete');

					if (!webhookId) {
						return;
					}

					void deleteWebhook(webhookId);
				});
			});
		};

		const loadWebhooks = async function () {
			const envId = getCurrentEnvironmentId();

			if (!envId) {
				return;
			}

			const response = await fetch('/api/company/webhooks/' + envId, {
				headers: authorizedHeaders(false),
			});

			if (response.status === 401) {
				clearSessionAndRedirectToLogin();
				return;
			}

			if (!response.ok) {
				setStatus('No se pudieron cargar los webhooks.', 'error');
				return;
			}

			const payload = await response.json().catch(function () {
				return {};
			});

			renderWebhooks(Array.isArray(payload?.data) ? payload.data : []);
		};

		const deleteWebhook = async function (webhookId) {
			const envId = getCurrentEnvironmentId();

			if (!envId) {
				return;
			}

			setStatus('Eliminando webhook...', 'info');

			const response = await fetch('/api/company/webhooks/' + envId + '/' + webhookId, {
				method: 'DELETE',
				headers: authorizedHeaders(false),
			});

			if (!response.ok) {
				setStatus('No se pudo eliminar el webhook.', 'error');
				return;
			}

			setStatus('Webhook eliminado correctamente.', 'success');
			void loadWebhooks();
		};

		const loadEnvironmentData = async function () {
			setStatus('Cargando configuracion del ambiente...', 'info');
			await loadSettings();
			await loadWebhooks();
			hideStatus();
		};

		envSelect.addEventListener('change', function () {
			const selected = envSelect.value;

			if (!selected) {
				return;
			}

			currentEnvironmentId = Number(selected);
			localStorage.setItem(selectedEnvironmentKey, selected);
			void loadEnvironmentData();
		});

		actionTypeSelect.addEventListener('change', toggleAllowMultipleAvailability);

		settingsForm.addEventListener('submit', async function (event) {
			event.preventDefault();

			const envId = getCurrentEnvironmentId();

			if (!envId) {
				return;
			}

			setStatus('Guardando configuracion...', 'info');

			const payload = {
				action_type_id: Number(actionTypeSelect.value || 0),
				allow_multiple_confirmation: Boolean(allowMultipleInput.checked),
				link_expiration_hours: Number(linkExpirationInput.value || 0),
				access_method_id: Number(accessMethodSelect.value || 0),
				ai_tone_id: Number(aiToneSelect.value || 0),
				return_button: {
					text: returnButtonTextInput.value,
					url: returnButtonUrlInput.value,
				},
				allow_calendar_dates: Boolean(allowCalendarInput.checked),
				send_summary_pdf_by_email: Boolean(sendSummaryInput.checked),
			};

			const response = await fetch('/api/company/configs/' + envId, {
				method: 'PUT',
				headers: authorizedHeaders(true),
				body: JSON.stringify(payload),
			});

			if (response.status === 401) {
				clearSessionAndRedirectToLogin();
				return;
			}

			if (!response.ok) {
				const errorPayload = await response.json().catch(function () {
					return {};
				});

				const validationErrors = errorPayload?.errors;
				const firstError = validationErrors
					? Object.values(validationErrors)[0]?.[0]
					: null;

				setStatus(firstError || 'No se pudo guardar la configuracion.', 'error');
				return;
			}

			setStatus('Configuracion guardada correctamente.', 'success');
			await loadSettings();
		});

		webhookForm.addEventListener('submit', async function (event) {
			event.preventDefault();

			const envId = getCurrentEnvironmentId();

			if (!envId) {
				return;
			}

			const events = String(webhookEventsInput.value || '')
				.split(',')
				.map(function (item) {
					return item.trim();
				})
				.filter(Boolean);

			setStatus('Creando webhook...', 'info');

			const response = await fetch('/api/company/webhooks/' + envId, {
				method: 'POST',
				headers: authorizedHeaders(true),
				body: JSON.stringify({
					name: webhookNameInput.value,
					url: webhookUrlInput.value,
					secret: webhookSecretInput.value || null,
					events: events,
					is_active: webhookActiveInput.checked,
				}),
			});

			if (!response.ok) {
				setStatus('No se pudo crear el webhook.', 'error');
				return;
			}

			webhookForm.reset();
			webhookActiveInput.checked = true;
			setStatus('Webhook creado correctamente.', 'success');
			await loadWebhooks();
		});

		const boot = async function () {
			setStatus('Cargando catalogos...', 'info');

			const ok = await loadCatalogs();

			if (!ok) {
				return;
			}

			await loadEnvironmentData();
		};

		void boot();
	})();
</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
	<div id="company-home-status" class="mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800">
		Cargando catalogos...
	</div>

	<div class="mb-6 rounded-xl border border-gray-200 bg-white p-5">
		<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
			<div>
				<h1 class="text-xl font-semibold" style="color:#0f1f3d;">Configuracion de empresa</h1>
				<p class="text-sm text-gray-500">Administra reglas, tono y webhooks por ambiente.</p>
			</div>
			<div class="w-full sm:w-64">
				<label for="company-environment-select" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Ambiente</label>
				<select id="company-environment-select"
					class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
				</select>
			</div>
		</div>
	</div>

	<div class="mb-6 rounded-xl border border-gray-200 bg-white p-5">
		<h2 class="mb-4 text-sm font-semibold" style="color:#0f1f3d;">Reglas de experiencia</h2>

		<form id="company-settings-form" class="grid grid-cols-1 gap-4 md:grid-cols-2">
			<div>
				<label for="action_type_id" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Tipo de accion</label>
				<select id="action_type_id" required
					class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100"></select>
			</div>

			<div>
				<label for="access_method_id" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Metodo de acceso</label>
				<select id="access_method_id" required
					class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100"></select>
			</div>

			<div>
				<label for="ai_tone_id" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Tono de IA</label>
				<select id="ai_tone_id" required
					class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100"></select>
			</div>

			<div>
				<label for="link_expiration_hours" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Horas de expiracion del link</label>
				<input id="link_expiration_hours" type="number" min="1" max="720" required
					class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
			</div>

			<div>
				<label for="return_button_text" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Texto boton volver</label>
				<input id="return_button_text" type="text" maxlength="120" required
					class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
			</div>

			<div>
				<label for="return_button_url" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">URL boton volver</label>
				<input id="return_button_url" type="url" maxlength="2048" required
					class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
			</div>

			<label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700">
				<input id="allow_multiple_confirmation" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-cyan-500 focus:ring-cyan-300">
				Permitir confirmacion multiple
			</label>

			<label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700">
				<input id="allow_calendar_dates" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-cyan-500 focus:ring-cyan-300">
				Habilitar fechas en calendario
			</label>

			<label class="md:col-span-2 flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700">
				<input id="send_summary_pdf_by_email" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-cyan-500 focus:ring-cyan-300">
				Enviar PDF de resumen al correo
			</label>

			<div class="md:col-span-2">
				<button type="submit"
					class="rounded-lg bg-cyan-500 px-4 py-2 text-sm font-medium text-white hover:opacity-90">
					Guardar configuracion
				</button>
			</div>
		</form>
	</div>

	<div class="rounded-xl border border-gray-200 bg-white p-5">
		<h2 class="mb-4 text-sm font-semibold" style="color:#0f1f3d;">Webhooks</h2>

		<form id="company-webhook-form" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-5">
			<input id="webhook_name" type="text" maxlength="120" required placeholder="Nombre"
				class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
			<input id="webhook_url" type="url" required placeholder="https://dominio.com/webhook"
				class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100 md:col-span-2">
			<input id="webhook_secret" type="text" maxlength="120" placeholder="Secret (opcional)"
				class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
			<input id="webhook_events" type="text" placeholder="Eventos separados por coma"
				class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
			<label class="md:col-span-4 flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700">
				<input id="webhook_is_active" type="checkbox" checked class="h-4 w-4 rounded border-gray-300 text-cyan-500 focus:ring-cyan-300">
				Webhook activo
			</label>
			<div class="md:col-span-1 md:text-right">
				<button type="submit"
					class="w-full md:w-auto rounded-lg bg-cyan-500 px-4 py-2 text-sm font-medium text-white hover:opacity-90">
					Agregar
				</button>
			</div>
		</form>

		<div class="overflow-x-auto rounded-xl border border-gray-200">
			<table class="min-w-full bg-white">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</th>
						<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">URL</th>
						<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Eventos</th>
						<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
						<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Accion</th>
					</tr>
				</thead>
				<tbody id="company-webhooks-body"></tbody>
			</table>
		</div>
	</div>
</div>
@endsection
