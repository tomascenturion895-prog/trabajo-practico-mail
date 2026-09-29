<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

// Páginas informativas
Route::view('/', 'welcome')->name('home');
Route::view('/acerca-de', 'pages.acerca')->name('acerca');

// Registro e inicio de sesión (solo invitados)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register.create');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Formulario de envío de correo (público, según el TP; si hay sesión, el envío queda en el historial)
Route::get('/mail', [MailController::class, 'formulario'])->name('mail.formulario');
Route::post('/mail/enviar', [MailController::class, 'enviar'])->name('mail.enviar');

// Historial de correos enviados (requiere sesión)
Route::middleware('auth')->prefix('historial')->name('historial.')->group(function () {
    Route::get('/', [HistorialController::class, 'index'])->name('index');
    Route::get('/{sentMail}', [HistorialController::class, 'show'])->name('show');
    Route::delete('/{sentMail}', [HistorialController::class, 'destroy'])->name('destroy');
});
