<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Jobs\SendWelcomeEmailJob;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // 1. Validaciones extra completas
        $validated = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:255'],
            'email'    => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique'       => 'Este correo electrónico ya se encuentra registrado.',
            'password.confirmed' => 'Las contraseñas ingresadas no coinciden.',
        ]);

        // 2. Guardar usuario en la Base de Datos
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3. Enviar correo de bienvenida mediante Brevo SMTP (en segundo plano, con Job dedicado)
        SendWelcomeEmailJob::dispatch($user);

        // 4. Retornar respuesta exitosa
        return redirect()->route('login')
            ->with('success', '¡Usuario registrado con éxito! Te enviamos un correo de bienvenida. Ya podés iniciar sesión.');
    }
}
