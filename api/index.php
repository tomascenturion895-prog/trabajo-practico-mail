<?php

/**
 * Punto de entrada de Laravel para Vercel (runtime vercel-php).
 * El sistema de archivos de Vercel es de solo lectura, salvo /tmp,
 * por eso se redirige el directorio "storage" a /tmp/storage.
 */

use Illuminate\Http\Request;

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

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($storage);

$app->handleRequest(Request::capture());
