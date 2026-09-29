# Trabajo Práctico – Laravel 13 + Brevo SMTP

Un solo proyecto con los tres trabajos:

1. **Registro de usuarios + Mailer Brevo + Colas** (`/register`) – guía "Registro de Usuarios y Mailer Brevo SMTP en Laravel 13".
2. **Envío de correo desde interfaz web** (`/mail`) – "Dando forma a nuestro envío de mail".
3. **Testing del módulo de envío** (`tests/Feature/MailTest.php`) – "Tutorial práctico: Testing del módulo de envío de correos".

## Puesta en marcha

```bash
composer install
npm install && npm run build
php artisan key:generate
# editar .env con tus credenciales de Brevo (SMTP key de 64 caracteres, NO la API key)
php artisan config:clear
php artisan migrate
php artisan serve
```

En otra terminal (para procesar los correos de bienvenida encolados):

```bash
php artisan queue:work
```

- Registro: http://127.0.0.1:8000/register
- Formulario de mail: http://127.0.0.1:8000/mail

## Tests

```bash
php artisan test
php artisan test --filter=MailTest
```

## Colas – comandos útiles
`php artisan queue:failed` · `php artisan queue:retry {id}` · `php artisan queue:retry all`
