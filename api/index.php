<?php

/**
 * Punto de entrada de Laravel para Vercel (runtime vercel-php).
 * El sistema de archivos de Vercel es de solo lectura, salvo /tmp,
 * por eso se redirige el directorio "storage" a /tmp/storage.
 */

use Illuminate\Http\Request;

// >>> DIAGNÓSTICO TEMPORAL: mostrar el error real en pantalla. BORRAR estas líneas cuando se resuelva. <<<
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = $_SERVER['APP_DEBUG'] = 'true';
ini_set('display_errors', '1');
error_reporting(E_ALL);
// >>> FIN DIAGNÓSTICO <<<

define('LARAVEL_START', microtime(true));

$storage = '/tmp/storage';

foreach ([
    'app/public',
    'framework/cache/data',
    'framework/sessions',
    'framework/views',
    'logs',
] as $dir) {
    if (! is_dir("$storage/$dir")) {
        mkdir("$storage/$dir", 0755, true);
    }
}

if (! is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0755, true);
}

try {
    require __DIR__ . '/../vendor/autoload.php';

    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $app->useStoragePath($storage);

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    // DIAGNÓSTICO TEMPORAL
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo get_class($e) . ': ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString();
}
