<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\MailController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas para el flujo de registro
Route::get('/register', [RegisterController::class, 'create'])->name('register.create');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// Rutas del formulario de envío de correo
Route::get('/mail', [MailController::class, 'formulario'])
    ->name('mail.formulario');

Route::post('/mail/enviar', [MailController::class, 'enviar'])
    ->name('mail.enviar');
