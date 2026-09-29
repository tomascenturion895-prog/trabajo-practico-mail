<x-layouts.app title="Iniciar sesión">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Iniciar sesión</h1>
            <p class="text-slate-500 mt-2">Accedé para ver tu historial de correos enviados.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
                @csrf

                <x-field name="email" type="email" label="Correo electrónico" required autofocus autocomplete="email"
                         placeholder="tu@correo.com" />

                <x-field name="password" type="password" label="Contraseña" required autocomplete="current-password" />

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Mantener la sesión iniciada
                </label>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition">
                    Ingresar
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-slate-500 mt-5">
            ¿Todavía no tenés cuenta?
            <a href="{{ route('register.create') }}" class="text-indigo-600 font-medium hover:underline">Registrate</a>
        </p>
    </div>
</x-layouts.app>
