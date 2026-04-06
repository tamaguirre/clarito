<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión — Clarito</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex" style="background-color: #f0f4f8;">

    {{-- Panel izquierdo decorativo --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12" style="background-color: #0f1f3d;">
        <div></div>
        <div>
            <h2 class="text-white text-3xl font-semibold leading-snug mb-4">
                Tu documento más difícil,<br>explicado en segundos.
            </h2>
            <p class="text-blue-200 text-sm leading-relaxed">
                Clarito analiza el lenguaje complejo y te entrega un resumen claro, directo y fácil de entender.
            </p>
        </div>
        <p class="text-blue-300 text-xs">&copy; {{ date('Y') }} Clarito — Proyecto de Tesis</p>
    </div>

    {{-- Panel derecho: formulario --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">

            {{-- Logo mobile --}}
            <div class="flex justify-center mb-6">
                <img src="{{ asset('img/logo.png') }}" alt="Clarito" class="h-28 w-auto">
            </div>

            <h1 class="text-2xl font-semibold mb-1" style="color: #0f1f3d;">Bienvenido de vuelta</h1>
            <p class="text-sm text-gray-500 mb-8">Ingresa para continuar simplificando tus documentos</p>

            <p id="login-general-error" class="mb-4 hidden rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600"></p>

            <form id="login-form" novalidate>

                {{-- Email --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                    <input id="email" type="email" name="email" required autofocus
                        placeholder="juan@correo.com"
                        class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 transition focus:ring-cyan-200 focus:border-cyan-400">
                    <p id="login-email-error" class="mt-1 hidden text-xs text-red-500"></p>
                </div>

                {{-- Contraseña --}}
                <div class="mb-5">
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="text-sm font-medium text-gray-700">Contraseña</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs hover:underline" style="color: #00bcd4;">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required
                        placeholder="Tu contraseña"
                        class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 transition focus:ring-cyan-200 focus:border-cyan-400">
                    <p id="login-password-error" class="mt-1 hidden text-xs text-red-500"></p>
                </div>

                {{-- Recordarme --}}
                <div class="flex items-center gap-2.5 mb-6">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="w-4 h-4 rounded accent-cyan-500">
                    <label for="remember_me" class="text-sm text-gray-600">Recordarme</label>
                </div>

                {{-- Submit --}}
                <button id="login-submit" type="submit"
                    class="w-full py-2.5 rounded-lg text-sm font-semibold text-white transition hover:opacity-90 active:scale-95"
                    style="background-color: #00bcd4;">
                    Iniciar sesión
                </button>

                <p class="text-center text-sm text-gray-500 mt-6">
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}" class="font-medium hover:underline" style="color: #00bcd4;">Regístrate gratis</a>
                </p>
            </form>
        </div>
    </div>

    <script>
    (function () {
        const existingToken = localStorage.getItem('access_token');
        const rawUser = localStorage.getItem('auth_user');

        const resolveHomeByRole = function (user) {
            if (user?.role?.name === 'company') {
                return '/company/home';
            }

            return '/upload';
        };

        if (existingToken) {
            try {
                const parsedUser = rawUser ? JSON.parse(rawUser) : null;
                window.location.replace(resolveHomeByRole(parsedUser));
                return;
            } catch {
                window.location.replace('/upload');
                return;
            }
        }

        const form = document.getElementById('login-form');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const submitButton = document.getElementById('login-submit');
        const generalError = document.getElementById('login-general-error');
        const emailError = document.getElementById('login-email-error');
        const passwordError = document.getElementById('login-password-error');

        if (!form || !emailInput || !passwordInput || !submitButton || !generalError || !emailError || !passwordError) {
            return;
        }

        const clearErrors = () => {
            generalError.classList.add('hidden');
            generalError.textContent = '';

            emailError.classList.add('hidden');
            emailError.textContent = '';

            passwordError.classList.add('hidden');
            passwordError.textContent = '';
        };

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            clearErrors();

            submitButton.setAttribute('disabled', 'true');

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({
                        email: emailInput.value,
                        password: passwordInput.value,
                    }),
                });

                const payload = await response.json();

                if (response.status === 422) {
                    const errors = payload.errors || {};

                    if (errors.email && errors.email[0]) {
                        emailError.textContent = errors.email[0];
                        emailError.classList.remove('hidden');
                    }

                    if (errors.password && errors.password[0]) {
                        passwordError.textContent = errors.password[0];
                        passwordError.classList.remove('hidden');
                    }

                    if (!errors.email && !errors.password) {
                        generalError.textContent = 'No pudimos iniciar sesión. Revisa tus datos.';
                        generalError.classList.remove('hidden');
                    }

                    return;
                }

                if (!response.ok) {
                    generalError.textContent = 'No pudimos iniciar sesión. Intenta nuevamente.';
                    generalError.classList.remove('hidden');
                    return;
                }

                const accessToken = payload?.meta?.access_token;
                const tokenType = payload?.meta?.token_type || 'Bearer';
                const authUser = payload?.data || null;

                if (accessToken) {
                    localStorage.setItem('access_token', accessToken);
                    localStorage.setItem('token_type', tokenType);
                }

                if (authUser) {
                    localStorage.setItem('auth_user', JSON.stringify(authUser));
                }

                window.location.replace(resolveHomeByRole(authUser));
            } catch {
                generalError.textContent = 'No pudimos iniciar sesión. Intenta nuevamente.';
                generalError.classList.remove('hidden');
            } finally {
                submitButton.removeAttribute('disabled');
            }
        });
    })();
    </script>

</body>
</html>