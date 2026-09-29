<div>

    @if ($exito)
        <div role="alert" class="mb-5 p-4 rounded-lg bg-green-50 border border-green-300 text-green-800">
            {{ $exito }}
            @auth
                <a href="{{ route('historial.index') }}" class="font-semibold underline">Ver historial</a>
            @endauth
        </div>
    @endif

    @if ($fallo)
        <div role="alert" class="mb-5 p-4 rounded-lg bg-red-50 border border-red-300 text-red-800">
            {{ $fallo }}
        </div>
    @endif

    {{-- Indicador de pasos --}}
    <ol class="flex items-center gap-3 mb-5 text-sm font-medium">
        <li class="flex items-center gap-2 {{ $paso === 'editar' ? 'text-indigo-700' : 'text-slate-400' }}">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full {{ $paso === 'editar' ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-600' }}">1</span>
            Redactar
        </li>
        <li class="h-px flex-1 bg-slate-300"></li>
        <li class="flex items-center gap-2 {{ $paso === 'confirmar' ? 'text-indigo-700' : 'text-slate-400' }}">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full {{ $paso === 'confirmar' ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-600' }}">2</span>
            Confirmar
        </li>
    </ol>

    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">

        @if ($paso === 'editar')

            <form wire:submit="revisar" class="space-y-5" novalidate>

                {{-- Para --}}
                <div>
                    <label for="destinatario" class="block mb-1.5 text-sm font-semibold text-slate-700">
                        Correo destinatario <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="destinatario" wire:model.blur="destinatario" autocomplete="off"
                           placeholder="ejemplo@correo.com, otro@correo.com"
                           class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('destinatario') border-red-400 @else border-slate-300 @enderror">
                    <p class="mt-1 text-xs text-slate-400">Podés ingresar varias direcciones separadas por coma.</p>
                    @error('destinatario')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- CC / CCO --}}
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="cc" class="block mb-1.5 text-sm font-semibold text-slate-700">CC (con copia)</label>
                        <input type="text" id="cc" wire:model.blur="cc" autocomplete="off" placeholder="opcional"
                               class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('cc') border-red-400 @else border-slate-300 @enderror">
                        @error('cc')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="cco" class="block mb-1.5 text-sm font-semibold text-slate-700">CCO (copia oculta)</label>
                        <input type="text" id="cco" wire:model.blur="cco" autocomplete="off" placeholder="opcional"
                               class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('cco') border-red-400 @else border-slate-300 @enderror">
                        @error('cco')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    {{-- Nombre --}}
                    <div>
                        <label for="nombre" class="block mb-1.5 text-sm font-semibold text-slate-700">
                            Nombre del destinatario <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre" wire:model.blur="nombre" placeholder="Juan Pérez"
                               class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('nombre') border-red-400 @else border-slate-300 @enderror">
                        <p class="mt-1 text-xs text-slate-400">El nombre aparecerá en el correo.</p>
                        @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Teléfono --}}
                    <div>
                        <label for="telefono" class="block mb-1.5 text-sm font-semibold text-slate-700">Teléfono (opcional)</label>
                        <input type="text" id="telefono" wire:model.blur="telefono" placeholder="Ej: 3704123456"
                               class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('telefono') border-red-400 @else border-slate-300 @enderror">
                        <p class="mt-1 text-xs text-slate-400">Mínimo 8 caracteres.</p>
                        @error('telefono')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Asunto --}}
                <div>
                    <label for="asunto" class="block mb-1.5 text-sm font-semibold text-slate-700">
                        Asunto <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="asunto" wire:model.blur="asunto" placeholder="Ingrese el asunto"
                           class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('asunto') border-red-400 @else border-slate-300 @enderror">
                    @error('asunto')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Mensaje --}}
                <div>
                    <label for="mensaje" class="block mb-1.5 text-sm font-semibold text-slate-700">
                        Mensaje <span class="text-red-500">*</span>
                    </label>
                    <textarea id="mensaje" rows="7" wire:model.live.debounce.400ms="mensaje"
                              placeholder="Escriba aquí el contenido del correo..."
                              class="w-full px-4 py-2.5 border rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('mensaje') border-red-400 @else border-slate-300 @enderror"></textarea>
                    <div class="mt-1 flex justify-between text-xs">
                        <span class="text-slate-400">Podés escribir hasta 5000 caracteres.</span>
                        <span class="{{ mb_strlen($mensaje) > 5000 ? 'text-red-600' : 'text-slate-400' }}">{{ mb_strlen($mensaje) }}/5000</span>
                    </div>
                    @error('mensaje')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Adjuntos --}}
                <div>
                    <label for="archivos" class="block mb-1.5 text-sm font-semibold text-slate-700">Archivos adjuntos (opcional)</label>
                    <input type="file" id="archivos" wire:model="archivos" multiple
                           class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-slate-400">Hasta 3 archivos de 2 MB (pdf, office, txt, csv, imágenes, zip).</p>
                    <p wire:loading wire:target="archivos" class="mt-1 text-sm text-indigo-600">Subiendo archivos…</p>

                    @error('archivos')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('archivos.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                    @if (count($archivos))
                        <ul class="mt-3 space-y-2">
                            @foreach ($archivos as $i => $archivo)
                                <li wire:key="archivo-{{ $i }}-{{ $archivo->getFilename() }}"
                                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg bg-slate-50 border border-slate-200 text-sm">
                                    <span class="truncate">{{ $archivo->getClientOriginalName() }}
                                        <span class="text-slate-400">({{ number_format($archivo->getSize() / 1024, 0) }} KB)</span>
                                    </span>
                                    <button type="button" wire:click="quitarArchivo({{ $i }})"
                                            class="text-red-600 hover:underline shrink-0">Quitar</button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Botones --}}
                <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">
                    <button type="button" wire:click="limpiar"
                            class="text-center bg-white hover:bg-slate-50 text-slate-700 font-semibold border border-slate-300 py-3 px-6 rounded-lg transition">
                        Limpiar
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="revisar,archivos"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-semibold py-3 px-6 rounded-lg transition">
                        Enviar correo
                    </button>
                </div>
            </form>

        @else

            {{-- Pantalla de confirmación --}}
            <h2 class="text-xl font-semibold text-slate-900">Revisá el correo antes de enviarlo</h2>
            <p class="text-sm text-slate-500 mt-1">Verificá que los datos sean correctos. Una vez enviado, no se puede deshacer.</p>

            <dl class="mt-6 divide-y divide-slate-200 text-sm">
                <div class="py-3 grid sm:grid-cols-4 gap-1">
                    <dt class="font-semibold text-slate-600">Para</dt>
                    <dd class="sm:col-span-3 flex flex-wrap gap-1.5">
                        @foreach ($listaPara as $c)
                            <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">{{ $c }}</span>
                        @endforeach
                    </dd>
                </div>

                @if (count($listaCc))
                    <div class="py-3 grid sm:grid-cols-4 gap-1">
                        <dt class="font-semibold text-slate-600">CC</dt>
                        <dd class="sm:col-span-3 flex flex-wrap gap-1.5">
                            @foreach ($listaCc as $c)
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $c }}</span>
                            @endforeach
                        </dd>
                    </div>
                @endif

                @if (count($listaCco))
                    <div class="py-3 grid sm:grid-cols-4 gap-1">
                        <dt class="font-semibold text-slate-600">CCO</dt>
                        <dd class="sm:col-span-3 flex flex-wrap gap-1.5">
                            @foreach ($listaCco as $c)
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $c }}</span>
                            @endforeach
                        </dd>
                    </div>
                @endif

                <div class="py-3 grid sm:grid-cols-4 gap-1">
                    <dt class="font-semibold text-slate-600">Nombre</dt>
                    <dd class="sm:col-span-3">{{ $nombre }}</dd>
                </div>

                @if ($telefono !== '')
                    <div class="py-3 grid sm:grid-cols-4 gap-1">
                        <dt class="font-semibold text-slate-600">Teléfono</dt>
                        <dd class="sm:col-span-3">{{ $telefono }}</dd>
                    </div>
                @endif

                <div class="py-3 grid sm:grid-cols-4 gap-1">
                    <dt class="font-semibold text-slate-600">Asunto</dt>
                    <dd class="sm:col-span-3">{{ $asunto }}</dd>
                </div>

                <div class="py-3 grid sm:grid-cols-4 gap-1">
                    <dt class="font-semibold text-slate-600">Mensaje</dt>
                    <dd class="sm:col-span-3 whitespace-pre-line">{{ $mensaje }}</dd>
                </div>

                @if (count($archivos))
                    <div class="py-3 grid sm:grid-cols-4 gap-1">
                        <dt class="font-semibold text-slate-600">Adjuntos</dt>
                        <dd class="sm:col-span-3">
                            <ul class="space-y-1">
                                @foreach ($archivos as $archivo)
                                    <li>📎 {{ $archivo->getClientOriginalName() }}
                                        <span class="text-slate-400">({{ number_format($archivo->getSize() / 1024, 0) }} KB)</span>
                                    </li>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                @endif
            </dl>

            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-6">
                <button type="button" wire:click="volver" wire:loading.attr="disabled"
                        class="whitespace-nowrap bg-white hover:bg-slate-50 text-slate-700 font-semibold border border-slate-300 py-3 px-6 rounded-lg transition">
                    Volver a editar
                </button>

                <button type="button" wire:click="enviar" wire:loading.attr="disabled"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-semibold py-3 px-6 rounded-lg transition">
                    <span wire:loading.remove wire:target="enviar">Confirmar y enviar</span>
                    <span wire:loading wire:target="enviar">Enviando…</span>
                </button>
            </div>

        @endif
    </div>
</div>
