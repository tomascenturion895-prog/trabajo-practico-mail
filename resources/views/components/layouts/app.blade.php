@props(['title' => null])
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-slate-100 text-slate-800 antialiased">

    {{-- Barra de navegación --}}
    @php
        $link = fn (string $patron) => request()->routeIs($patron)
            ? 'bg-indigo-50 text-indigo-700'
            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900';
    @endphp

    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <nav class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-4">

            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-slate-900">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </span>
                <span>Mail<span class="text-indigo-600">Laravel</span></span>
            </a>

            {{-- Menú escritorio --}}
            <div class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg {{ $link('home') }}">Inicio</a>
                <a href="{{ route('mail.formulario') }}" class="px-3 py-2 rounded-lg {{ $link('mail.*') }}">Enviar correo</a>
                @auth
                    <a href="{{ route('historial.index') }}" class="px-3 py-2 rounded-lg {{ $link('historial.*') }}">Historial</a>
                @endauth
                <a href="{{ route('acerca') }}" class="px-3 py-2 rounded-lg {{ $link('acerca') }}">Acerca de</a>
            </div>

            <div class="hidden md:flex items-center gap-2 text-sm">
                @auth
                    <span class="flex items-center gap-2 text-slate-600">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-semibold">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        {{ auth()->user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 font-medium hover:bg-slate-50 transition">
                            Salir
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-slate-100">Iniciar sesión</a>
                    <a href="{{ route('register.create') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">Registrarse</a>
                @endauth
            </div>

            {{-- Botón menú móvil --}}
            <button type="button" id="menu-toggle" aria-label="Abrir menú" aria-expanded="false"
                    class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </nav>

        {{-- Menú móvil --}}
        <div id="menu-movil" class="hidden md:hidden border-t border-slate-200 px-4 py-3 space-y-1 text-sm font-medium">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg {{ $link('home') }}">Inicio</a>
            <a href="{{ route('mail.formulario') }}" class="block px-3 py-2 rounded-lg {{ $link('mail.*') }}">Enviar correo</a>
            @auth
                <a href="{{ route('historial.index') }}" class="block px-3 py-2 rounded-lg {{ $link('historial.*') }}">Historial</a>
            @endauth
            <a href="{{ route('acerca') }}" class="block px-3 py-2 rounded-lg {{ $link('acerca') }}">Acerca de</a>

            <div class="pt-2 mt-2 border-t border-slate-200">
                @auth
                    <p class="px-3 py-1 text-slate-500">{{ auth()->user()->name }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Salir</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100">Iniciar sesión</a>
                    <a href="{{ route('register.create') }}" class="block px-3 py-2 rounded-lg text-indigo-700 hover:bg-indigo-50">Registrarse</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 w-full max-w-6xl mx-auto px-4 py-8">

        @foreach (['success' => 'green', 'error' => 'red', 'status' => 'blue'] as $clave => $color)
            @if (session($clave))
                <div role="alert" class="mb-6 p-4 rounded-lg border
                    @if($color === 'green') bg-green-50 border-green-300 text-green-800
                    @elseif($color === 'red') bg-red-50 border-red-300 text-red-800
                    @else bg-blue-50 border-blue-300 text-blue-800 @endif">
                    {{ session($clave) }}
                </div>
            @endif
        @endforeach

        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 py-5 text-sm text-slate-500 flex flex-col sm:flex-row justify-between gap-2">
            <p>© {{ date('Y') }} MailLaravel · Laravel + Brevo SMTP</p>
            <p>Trabajo práctico: registro con cola, envío de correos y tests</p>
        </div>
    </footer>

    <script>
        (function () {
            var boton = document.getElementById('menu-toggle');
            var menu = document.getElementById('menu-movil');
            if (!boton || !menu) return;
            boton.addEventListener('click', function () {
                var abierto = menu.classList.toggle('hidden') === false;
                boton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
            });
        })();
    </script>
</body>

</html>
