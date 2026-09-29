<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentMail extends Model
{
    public const ENVIADO = 'enviado';
    public const FALLIDO = 'fallido';

    protected $fillable = [
        'user_id', 'destinatario', 'nombre', 'telefono', 'cc', 'cco',
        'asunto', 'mensaje', 'adjuntos', 'estado', 'error',
    ];

    protected function casts(): array
    {
        return [
            'adjuntos' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fueEnviado(): bool
    {
        return $this->estado === self::ENVIADO;
    }
}
