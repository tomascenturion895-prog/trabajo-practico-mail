<x-layouts.app title="Detalle del correo">
    <div class="max-w-3xl mx-auto">

        <a href="{{ route('historial.index') }}" class="text-sm text-indigo-600 hover:underline">← Volver al historial</a>

        <div class="mt-3 bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <div class="flex items-start justify-between gap-4">
                <h1 class="text-2xl font-bold text-slate-900">{{ $correo->asunto }}</h1>
                @if ($correo->fueEnviado())
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Enviado</span>
                @else
                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Fallido</span>
                @endif
            </div>
            <p class="text-sm text-slate-500 mt-1">{{ $correo->created_at->format('d/m/Y H:i') }}</p>

            @unless ($correo->fueEnviado())
                <div class="mt-4 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    <strong>Motivo:</strong> {{ $correo->error ?? 'Error desconocido.' }}
                </div>
            @endunless

            <dl class="mt-6 divide-y divide-slate-200 text-sm">
                <div class="py-3 grid sm:grid-cols-4 gap-1"><dt class="font-semibold text-slate-600">Para</dt><dd class="sm:col-span-3 break-words">{{ $correo->destinatario }}</dd></div>
                @if ($correo->cc)<div class="py-3 grid sm:grid-cols-4 gap-1"><dt class="font-semibold text-slate-600">CC</dt><dd class="sm:col-span-3 break-words">{{ $correo->cc }}</dd></div>@endif
                @if ($correo->cco)<div class="py-3 grid sm:grid-cols-4 gap-1"><dt class="font-semibold text-slate-600">CCO</dt><dd class="sm:col-span-3 break-words">{{ $correo->cco }}</dd></div>@endif
                <div class="py-3 grid sm:grid-cols-4 gap-1"><dt class="font-semibold text-slate-600">Nombre</dt><dd class="sm:col-span-3">{{ $correo->nombre }}</dd></div>
                @if ($correo->telefono)<div class="py-3 grid sm:grid-cols-4 gap-1"><dt class="font-semibold text-slate-600">Teléfono</dt><dd class="sm:col-span-3">{{ $correo->telefono }}</dd></div>@endif
                <div class="py-3 grid sm:grid-cols-4 gap-1"><dt class="font-semibold text-slate-600">Mensaje</dt><dd class="sm:col-span-3 whitespace-pre-line">{{ $correo->mensaje }}</dd></div>
                @if ($correo->adjuntos)
                    <div class="py-3 grid sm:grid-cols-4 gap-1">
                        <dt class="font-semibold text-slate-600">Adjuntos</dt>
                        <dd class="sm:col-span-3">
                            @foreach ($correo->adjuntos as $a)
                                <p>📎 {{ $a['name'] }} <span class="text-slate-400">({{ number_format(($a['size'] ?? 0) / 1024, 0) }} KB)</span></p>
                            @endforeach
                        </dd>
                    </div>
                @endif
            </dl>

            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('mail.formulario', ['reenviar' => $correo->id]) }}"
                   class="text-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                    Reenviar / usar como base
                </a>

                <form method="POST" action="{{ route('historial.destroy', $correo) }}"
                      onsubmit="return confirm('¿Eliminar este registro del historial?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-5 py-2.5 rounded-lg border border-red-300 text-red-700 font-medium hover:bg-red-50 transition">
                        Eliminar registro
                    </button>
                </form>
            </div>
            <p class="mt-3 text-xs text-slate-400">Los adjuntos no se guardan: solo queda el nombre y tamaño. Para reenviar, volvé a adjuntarlos.</p>
        </div>
    </div>
</x-layouts.app>
