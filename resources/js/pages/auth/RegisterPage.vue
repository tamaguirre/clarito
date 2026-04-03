<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue';

type EducationLevel = {
    id: number;
    name: string;
};

type Condition = {
    id: number;
    name: string;
};

type RegisterForm = {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    birth_date: string;
    education_level_id: string;
    conditions: number[];
};

type Errors = Record<string, string[]>;

const step = ref(1);
const submitting = ref(false);
const generalError = ref('');

const educationLevels = ref<EducationLevel[]>([]);
const conditionsCatalog = ref<Condition[]>([]);

const form = reactive<RegisterForm>({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    birth_date: '',
    education_level_id: '',
    conditions: [],
});

const errors = reactive<Errors>({});

const canGoToStepTwo = computed(() => {
    return (
        form.name.trim().length > 0 &&
        form.email.trim().length > 0 &&
        form.password.length >= 8 &&
        form.password_confirmation.length >= 8
    );
});

function clearErrors(): void {
    for (const key of Object.keys(errors)) {
        delete errors[key];
    }

    generalError.value = '';
}

function firstError(key: keyof RegisterForm): string {
    return errors[key]?.[0] ?? '';
}

function validateStepOne(): boolean {
    clearErrors();

    if (!form.name.trim()) {
        errors.name = ['El nombre es obligatorio.'];
    }

    if (!form.email.trim()) {
        errors.email = ['El correo es obligatorio.'];
    }

    if (form.password.length < 8) {
        errors.password = ['La contraseña debe tener al menos 8 caracteres.'];
    }

    if (form.password_confirmation !== form.password) {
        errors.password_confirmation = ['Las contraseñas no coinciden.'];
    }

    return Object.keys(errors).length === 0;
}

function toggleCondition(conditionId: number): void {
    if (form.conditions.includes(conditionId)) {
        form.conditions = form.conditions.filter((id) => id !== conditionId);
        return;
    }

    form.conditions = [...form.conditions, conditionId];
}

function goToStepTwo(): void {
    if (!validateStepOne()) {
        return;
    }

    step.value = 2;
}

async function loadCatalogs(): Promise<void> {
    const [educationResponse, conditionsResponse] = await Promise.all([
        fetch('/api/education-levels', {
            headers: {
                Accept: 'application/json',
            },
        }),
        fetch('/api/conditions', {
            headers: {
                Accept: 'application/json',
            },
        }),
    ]);

    if (!educationResponse.ok || !conditionsResponse.ok) {
        throw new Error('No se pudieron cargar los catálogos.');
    }

    const educationPayload = (await educationResponse.json()) as { data: EducationLevel[] };
    const conditionsPayload = (await conditionsResponse.json()) as { data: Condition[] };

    educationLevels.value = educationPayload.data;
    conditionsCatalog.value = conditionsPayload.data;
}

async function submitForm(): Promise<void> {
    clearErrors();
    submitting.value = true;

    try {
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                name: form.name,
                email: form.email,
                password: form.password,
                password_confirmation: form.password_confirmation,
                birth_date: form.birth_date,
                education_level_id: Number(form.education_level_id),
                conditions: form.conditions,
            }),
        });

        if (response.status === 422) {
            const payload = (await response.json()) as { errors?: Errors };

            Object.assign(errors, payload.errors ?? {});
            return;
        }

        if (!response.ok) {
            generalError.value = 'No pudimos completar el registro. Intenta nuevamente.';
            return;
        }

        window.location.href = '/login';
    } catch {
        generalError.value = 'No pudimos completar el registro. Intenta nuevamente.';
    } finally {
        submitting.value = false;
    }
}

onMounted(async () => {
    try {
        await loadCatalogs();
    } catch {
        generalError.value = 'No se pudieron cargar los datos del formulario.';
    }
});
</script>

<template>
    <div>
        <div class="mb-4 flex items-center justify-between text-xs text-gray-500">
            <span>Paso {{ step }} de 2</span>
            <div class="flex gap-2">
                <span
                    class="h-2 w-12 rounded-full"
                    :class="step >= 1 ? 'bg-cyan-500' : 'bg-gray-200'"
                />
                <span
                    class="h-2 w-12 rounded-full"
                    :class="step >= 2 ? 'bg-cyan-500' : 'bg-gray-200'"
                />
            </div>
        </div>

        <p
            v-if="generalError"
            class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-600"
        >
            {{ generalError }}
        </p>

        <form class="space-y-5" @submit.prevent="submitForm">
            <template v-if="step === 1">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">Nombre completo</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        placeholder="Juan Perez"
                        class="w-full rounded-lg border px-4 py-2.5 text-sm transition focus:outline-none focus:ring-2"
                        :class="firstError('name') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:border-cyan-400 focus:ring-cyan-200'"
                    >
                    <p v-if="firstError('name')" class="mt-1 text-xs text-red-500">{{ firstError('name') }}</p>
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Correo electronico</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="juan@correo.com"
                        class="w-full rounded-lg border px-4 py-2.5 text-sm transition focus:outline-none focus:ring-2"
                        :class="firstError('email') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:border-cyan-400 focus:ring-cyan-200'"
                    >
                    <p v-if="firstError('email')" class="mt-1 text-xs text-red-500">{{ firstError('email') }}</p>
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Contrasena</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        placeholder="Minimo 8 caracteres"
                        class="w-full rounded-lg border px-4 py-2.5 text-sm transition focus:outline-none focus:ring-2"
                        :class="firstError('password') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:border-cyan-400 focus:ring-cyan-200'"
                    >
                    <p v-if="firstError('password')" class="mt-1 text-xs text-red-500">{{ firstError('password') }}</p>
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">Confirmar contrasena</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        placeholder="Repite tu contrasena"
                        class="w-full rounded-lg border px-4 py-2.5 text-sm transition focus:outline-none focus:ring-2"
                        :class="firstError('password_confirmation') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:border-cyan-400 focus:ring-cyan-200'"
                    >
                    <p v-if="firstError('password_confirmation')" class="mt-1 text-xs text-red-500">{{ firstError('password_confirmation') }}</p>
                </div>

                <button
                    type="button"
                    class="w-full rounded-lg py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                    style="background-color: #00bcd4;"
                    :disabled="!canGoToStepTwo"
                    @click="goToStepTwo"
                >
                    Continuar
                </button>
            </template>

            <template v-else>
                <div>
                    <label for="birth_date" class="mb-1.5 block text-sm font-medium text-gray-700">Fecha de nacimiento</label>
                    <input
                        id="birth_date"
                        v-model="form.birth_date"
                        type="date"
                        class="w-full rounded-lg border px-4 py-2.5 text-sm transition focus:outline-none focus:ring-2"
                        :class="firstError('birth_date') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:border-cyan-400 focus:ring-cyan-200'"
                    >
                    <p v-if="firstError('birth_date')" class="mt-1 text-xs text-red-500">{{ firstError('birth_date') }}</p>
                </div>

                <div>
                    <label for="education_level_id" class="mb-1.5 block text-sm font-medium text-gray-700">Nivel de educacion</label>
                    <select
                        id="education_level_id"
                        v-model="form.education_level_id"
                        class="w-full rounded-lg border px-4 py-2.5 text-sm transition focus:outline-none focus:ring-2"
                        :class="firstError('education_level_id') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:border-cyan-400 focus:ring-cyan-200'"
                    >
                        <option value="">Selecciona una opcion</option>
                        <option v-for="level in educationLevels" :key="level.id" :value="String(level.id)">
                            {{ level.name }}
                        </option>
                    </select>
                    <p v-if="firstError('education_level_id')" class="mt-1 text-xs text-red-500">{{ firstError('education_level_id') }}</p>
                </div>

                <fieldset>
                    <legend class="mb-2 block text-sm font-medium text-gray-700">Condiciones</legend>
                    <div class="max-h-40 space-y-2 overflow-y-auto rounded-lg border border-gray-200 p-3">
                        <label
                            v-for="condition in conditionsCatalog"
                            :key="condition.id"
                            class="flex cursor-pointer items-center gap-2 text-sm text-gray-700"
                        >
                            <input
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-cyan-500 focus:ring-cyan-200"
                                :checked="form.conditions.includes(condition.id)"
                                @change="toggleCondition(condition.id)"
                            >
                            <span>{{ condition.name }}</span>
                        </label>
                    </div>
                    <p v-if="firstError('conditions')" class="mt-1 text-xs text-red-500">{{ firstError('conditions') }}</p>
                    <p v-if="firstError('conditions.*')" class="mt-1 text-xs text-red-500">{{ firstError('conditions.*') }}</p>
                </fieldset>

                <div class="flex gap-3">
                    <button
                        type="button"
                        class="w-1/2 rounded-lg border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        @click="step = 1"
                    >
                        Volver
                    </button>
                    <button
                        type="submit"
                        class="w-1/2 rounded-lg py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                        style="background-color: #00bcd4;"
                        :disabled="submitting"
                    >
                        {{ submitting ? 'Creando...' : 'Crear cuenta' }}
                    </button>
                </div>
            </template>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Ya tienes cuenta?
            <a href="/login" class="font-medium hover:underline" style="color: #00bcd4;">Inicia sesion</a>
        </p>
    </div>
</template>
