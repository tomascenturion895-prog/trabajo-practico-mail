<?php

namespace App\Livewire;

use App\Models\SentMail;
use App\Services\MailSender;
use App\Support\EmailList;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class MailForm extends Component
{
    use WithFileUploads;

    public const MAX_DESTINATARIOS = 10;

    public string $destinatario = '';
    public string $cc = '';
    public string $cco = '';
    public string $nombre = '';
    public string $telefono = '';
    public string $asunto = '';
    public string $mensaje = '';

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $archivos = [];

    /** editar | confirmar */
    public string $paso = 'editar';

    public ?string $exito = null;
    public ?string $fallo = null;

    /** Permite "Reenviar" un correo del historial: /mail?reenviar=ID */
    public function mount(?int $reenviar = null): void
    {
        if ($reenviar && Auth::check()) {
            $correo = SentMail::where('user_id', Auth::id())->find($reenviar);

            if ($correo) {
                $this->destinatario = $correo->destinatario;
                $this->cc = (string) $correo->cc;
                $this->cco = (string) $correo->cco;
                $this->nombre = $correo->nombre;
                $this->telefono = (string) $correo->telefono;
                $this->asunto = $correo->asunto;
                $this->mensaje = $correo->mensaje;
            }
        }
    }

    protected function rules(): array
    {
        return [
            'destinatario' => ['required', 'string', 'max:1000', $this->listaDeCorreos()],
            'cc' => ['nullable', 'string', 'max:1000', $this->listaDeCorreos()],
            'cco' => ['nullable', 'string', 'max:1000', $this->listaDeCorreos()],
            'nombre' => ['required', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'min:8', 'max:50'],
            'asunto' => ['required', 'string', 'max:150'],
            'mensaje' => ['required', 'string', 'max:5000'],
            'archivos' => ['array', 'max:3'],
            'archivos.*' => ['file', 'max:2048', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,png,jpg,jpeg,zip'],
        ];
    }

    protected function messages(): array
    {
        return [
            'destinatario.required' => 'Ingresá al menos un destinatario.',
            'nombre.required' => 'Ingresá el nombre del destinatario.',
            'telefono.min' => 'El teléfono debe tener al menos 8 caracteres.',
            'asunto.required' => 'Ingresá un asunto.',
            'mensaje.required' => 'Escribí el mensaje.',
            'archivos.max' => 'Podés adjuntar hasta 3 archivos.',
            'archivos.*.max' => 'Cada archivo puede pesar hasta 2 MB.',
            'archivos.*.mimes' => 'Tipo de archivo no permitido (pdf, office, txt, csv, imágenes o zip).',
            'archivos.*.file' => 'El adjunto no es un archivo válido.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'cc' => 'CC',
            'cco' => 'CCO',
        ];
    }

    /** Regla: lista de correos separados por coma, punto y coma o espacio. */
    private function listaDeCorreos(): \Closure
    {
        return function (string $atributo, mixed $valor, \Closure $fail) {
            $correos = EmailList::parse($valor);

            if (count($correos) > self::MAX_DESTINATARIOS) {
                $fail('Máximo '.self::MAX_DESTINATARIOS.' direcciones por campo.');

                return;
            }

            foreach ($correos as $correo) {
                if (! filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $fail("«{$correo}» no es un correo válido.");

                    return;
                }
            }
        };
    }

    public function updated(string $propiedad): void
    {
        $this->exito = null;
        $this->fallo = null;

        // Validación en vivo campo por campo (los archivos se validan al subirse).
        if ($propiedad !== 'paso') {
            $this->validateOnly($propiedad);
        }
    }

    public function quitarArchivo(int $indice): void
    {
        unset($this->archivos[$indice]);
        $this->archivos = array_values($this->archivos);
    }

    /** Paso 1 → pantalla de confirmación */
    public function revisar(): void
    {
        $this->exito = null;
        $this->fallo = null;

        $this->validate();

        $this->paso = 'confirmar';
    }

    public function volver(): void
    {
        $this->paso = 'editar';
    }

    public function limpiar(): void
    {
        $this->reset(['destinatario', 'cc', 'cco', 'nombre', 'telefono', 'asunto', 'mensaje', 'archivos', 'paso', 'exito', 'fallo']);
        $this->resetValidation();
    }

    /** Paso 2 → envío real */
    public function enviar(MailSender $sender): void
    {
        $this->validate();

        try {
            $sender->enviar([
                'para' => EmailList::parse($this->destinatario),
                'cc' => EmailList::parse($this->cc),
                'cco' => EmailList::parse($this->cco),
                'nombre' => $this->nombre,
                'telefono' => $this->telefono !== '' ? $this->telefono : null,
                'asunto' => $this->asunto,
                'mensaje' => $this->mensaje,
            ], $this->archivos);
        } catch (Throwable $e) {
            $this->paso = 'editar';
            $this->fallo = 'No se pudo enviar el correo. Verifique la configuración SMTP.';

            return;
        }

        $this->limpiar();
        $this->exito = 'El correo fue enviado correctamente.';
    }

    public function render()
    {
        return view('livewire.mail-form', [
            'listaPara' => EmailList::parse($this->destinatario),
            'listaCc' => EmailList::parse($this->cc),
            'listaCco' => EmailList::parse($this->cco),
        ]);
    }
}
