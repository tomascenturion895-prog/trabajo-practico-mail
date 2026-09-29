<x-layouts.app title="Enviar correo">
    <div class="max-w-3xl mx-auto">

        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Envío de correo</h1>
            <p class="text-slate-500 mt-2">Laravel + Brevo SMTP</p>
        </div>

        @guest
            <div class="mb-5 p-4 rounded-lg bg-amber-50 border border-amber-300 text-amber-800 text-sm">
                Estás enviando como invitado: el correo se enviará, pero no quedará en ningún historial.
                <a href="{{ route('login') }}" class="font-semibold underline">Iniciá sesión</a> para guardarlo.
            </div>
        @endguest

        <livewire:mail-form :reenviar="request()->integer('reenviar') ?: null" />
    </div>
</x-layouts.app>
