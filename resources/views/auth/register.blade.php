<x-layouts.app title="Registro">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Formulario de Registro</h1>
            <p class="text-slate-500 mt-2">Creá tu cuenta y recibí un correo de bienvenida.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <form action="{{ route('register.store') }}" method="POST" class="space-y-5">
                @csrf

                <x-field name="name" label="Nombre Completo" autocomplete="name" placeholder="Juan Pérez" />
                <x-field name="email" type="email" label="Correo Electrónico" autocomplete="email" placeholder="tu@correo.com" />
                <x-field name="password" type="password" label="Contraseña" autocomplete="new-password"
                         hint="Mínimo 8 caracteres." />
                <x-field name="password_confirmation" type="password" label="Confirmar Contraseña" autocomplete="new-password" />

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition">
                    Registrarse
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-slate-500 mt-5">
            ¿Ya tenés cuenta?
            <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:underline">Iniciá sesión</a>
        </p>
    </div>
</x-layouts.app>
