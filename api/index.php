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

// Ruta de diagnóstico: /__env muestra qué variables llegan (secretos solo como "definida"/"vacía").
if (strpos($_SERVER['REQUEST_URI'] ?? '', '/__env') === 0) {
    header('Content-Type: text/plain; charset=utf-8');
    $visibles = ['APP_ENV','APP_DEBUG','APP_URL','MAIL_MAILER','MAIL_HOST','MAIL_PORT','MAIL_USERNAME','MAIL_FROM_ADDRESS','MAIL_FROM_NAME','DB_CONNECTION','DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','MYSQL_ATTR_SSL_CA','SESSION_DRIVER','QUEUE_CONNECTION'];
    $secretas = ['APP_KEY','MAIL_PASSWORD','DB_PASSWORD'];
    foreach ($visibles as $n) {
        $v = getenv($n);
        echo str_pad($n, 22) . ($v === false ? 'NO LLEGA' : ($v === '' ? '(vacía)' : $v)) . "\n";
    }
    foreach ($secretas as $n) {
        $v = getenv($n);
        echo str_pad($n, 22) . ($v === false ? 'NO LLEGA' : ($v === '' ? '(vacía)' : 'definida (' . strlen($v) . ' caracteres)')) . "\n";
    }
    echo "\ncerts/cacert.pem existe: " . (file_exists(__DIR__ . '/../certs/cacert.pem') ? 'SI' : 'NO') . "\n";
    exit;
}

// Si APP_KEY no llega a la función, mostrar qué variables (solo NOMBRES, nunca valores) sí llegan.
if (! getenv('APP_KEY')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    $nombres = array_keys(getenv());
    sort($nombres);
    echo "APP_KEY NO llega a la función.\n\nVariables recibidas (solo nombres):\n- " . implode("\n- ", $nombres);
    exit;
}
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
