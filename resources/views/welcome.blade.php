<x-layouts.app title="Inicio">

    {{-- Hero --}}
    <section class="text-center py-10 md:py-16">
        <span class="inline-block px-3 py-1 mb-4 text-xs font-semibold tracking-wide uppercase rounded-full bg-indigo-100 text-indigo-700">
            Laravel · Brevo SMTP · Livewire
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-slate-900 leading-tight">
            Envío de correos con <span class="text-indigo-600">registro, cola e historial</span>
        </h1>
        <p class="mt-4 max-w-2xl mx-auto text-lg text-slate-600">
            Registrate, recibí tu correo de bienvenida, enviá mensajes con varios destinatarios y adjuntos,
            y consultá todo lo que enviaste desde tu historial.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('mail.formulario') }}"
               class="px-6 py-3 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                Enviar un correo
            </a>
            @auth
                <a href="{{ route('historial.index') }}"
                   class="px-6 py-3 rounded-lg bg-white border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition">
                    Ver mi historial
                </a>
            @else
                <a href="{{ route('register.create') }}"
                   class="px-6 py-3 rounded-lg bg-white border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition">
                    Crear una cuenta
                </a>
            @endauth
        </div>
    </section>

    {{-- Flujo --}}
    <section class="grid gap-5 md:grid-cols-3">
        @php
            $pasos = [
                ['1', 'Registrate', 'Creá tu cuenta. Un job en cola envía el correo de bienvenida por Brevo.'],
                ['2', 'Enviá correos', 'Para, CC y CCO, archivos adjuntos y una pantalla de confirmación antes de enviar.'],
                ['3', 'Revisá el historial', 'Cada envío queda guardado: buscá, filtrá, reenviá o eliminá registros.'],
            ];
        @endphp

        @foreach ($pasos as [$n, $titulo, $texto])
            <div class="bg-white rounded-2xl shadow p-6">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-white font-bold">{{ $n }}</span>
                <h2 class="mt-4 text-lg font-semibold text-slate-900">{{ $titulo }}</h2>
                <p class="mt-2 text-slate-600">{{ $texto }}</p>
            </div>
        @endforeach
    </section>
</x-layouts.app>
