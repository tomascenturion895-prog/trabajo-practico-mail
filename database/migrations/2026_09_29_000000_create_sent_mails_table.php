<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sent_mails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('destinatario');          // destinatarios "Para" separados por coma
            $table->string('nombre', 100);
            $table->string('telefono', 50)->nullable();
            $table->text('cc')->nullable();
            $table->text('cco')->nullable();
            $table->string('asunto', 150);
            $table->text('mensaje');
            $table->json('adjuntos')->nullable();    // [{name, size}]
            $table->string('estado', 20)->default('enviado'); // enviado | fallido
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_mails');
    }
};
