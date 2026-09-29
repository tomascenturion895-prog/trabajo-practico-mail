<x-layouts.app title="Historial">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Historial de correos</h1>
            <p class="text-slate-500 mt-1">Todo lo que enviaste con tu cuenta, {{ auth()->user()->name }}.</p>
        </div>
        <a href="{{ route('mail.formulario') }}"
           class="inline-flex justify-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
            + Nuevo correo
        </a>
    </div>

    {{-- Resumen --}}
    <div class="grid gap-4 sm:grid-cols-3 mb-6">
        @foreach ([['Total', $total, 'text-slate-900'], ['Enviados', $enviados, 'text-green-600'], ['Fallidos', $fallidos, 'text-red-600']] as [$t, $n, $c])
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-slate-500">{{ $t }}</p>
                <p class="text-3xl font-bold {{ $c }}">{{ $n }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('historial.index') }}" class="flex flex-col sm:flex-row gap-3 mb-4">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por asunto, destinatario o nombre…"
               class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select name="estado" class="px-4 py-2.5 border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todos los estados</option>
            <option value="enviado" @selected(request('estado') === 'enviado')>Enviados</option>
            <option value="fallido" @selected(request('estado') === 'fallido')>Fallidos</option>
        </select>
        <button type="submit" class="px-5 py-2.5 rounded-lg bg-slate-800 text-white font-medium hover:bg-slate-900 transition">Filtrar</button>
        @if (request()->hasAny(['q', 'estado']))
            <a href="{{ route('historial.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 bg-white text-center text-slate-700 hover:bg-slate-50">Limpiar</a>
        @endif
    </form>

    @if ($correos->isEmpty())
        <div class="bg-white rounded-2xl shadow p-10 text-center">
            <p class="text-lg font-semibold text-slate-800">
                {{ request()->hasAny(['q', 'estado']) ? 'No hay resultados para ese filtro.' : 'Todavía no enviaste ningún correo.' }}
            </p>
            <p class="text-slate-500 mt-1">Cuando envíes uno desde tu cuenta, va a aparecer acá.</p>
            <a href="{{ route('mail.formulario') }}" class="inline-block mt-5 px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">Enviar mi primer correo</a>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Para</th>
                            <th class="px-4 py-3">Asunto</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($correos as $correo)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 whitespace-nowrap text-slate-500">{{ $correo->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 max-w-[14rem] truncate" title="{{ $correo->destinatario }}">{{ $correo->destinatario }}</td>
                                <td class="px-4 py-3 max-w-[18rem] truncate font-medium text-slate-800">
                                    {{ $correo->asunto }}
                                    @if ($correo->adjuntos) <span title="Con adjuntos">📎</span> @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($correo->fueEnviado())
                                        <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Enviado</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Fallido</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('historial.show', $correo) }}" class="text-indigo-600 font-medium hover:underline">Ver</a>
                                    <span class="text-slate-300 mx-1">|</span>
                                    <a href="{{ route('mail.formulario', ['reenviar' => $correo->id]) }}" class="text-indigo-600 font-medium hover:underline">Reenviar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">{{ $correos->links() }}</div>
    @endif
</x-layouts.app>
