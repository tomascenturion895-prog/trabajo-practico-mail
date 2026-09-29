<x-layouts.app title="Acerca de">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-slate-900">Acerca de este proyecto</h1>
        <p class="mt-3 text-slate-600">
            Un único proyecto Laravel que reúne los tres trabajos prácticos: registro de usuarios con correo de
            bienvenida en cola, formulario de envío de correos y su batería de tests.
        </p>

        <div class="mt-8 grid gap-5 sm:grid-cols-2">
            @php
                $items = [
                    ['Registro + cola', 'Al registrarse se despacha SendWelcomeEmailJob (3 intentos) que envía el WelcomeUserMail.'],
                    ['Mail con vista', 'TestBrevoMail renderiza la vista emails.test-brevo con nombre, asunto y mensaje.'],
                    ['Formulario Livewire', 'Para/CC/CCO, adjuntos, validación en vivo y confirmación previa al envío.'],
                    ['Historial', 'Cada envío (exitoso o fallido) se guarda y se puede consultar por usuario.'],
                    ['Tests', 'Pruebas de feature con Mail::fake() para validación, envío, destinatarios y login.'],
                    ['Despliegue', 'Vercel + TiDB Cloud (MySQL) + Brevo SMTP.'],
                ];
            @endphp
            @foreach ($items as [$t, $d])
                <div class="bg-white rounded-xl shadow p-5">
                    <h2 class="font-semibold text-slate-900">{{ $t }}</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ $d }}</p>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
