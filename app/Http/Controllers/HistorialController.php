<?php

namespace App\Http\Controllers;

use App\Models\SentMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $consulta = $usuario->sentMails()->latest();

        if ($buscar = trim((string) $request->query('q'))) {
            $consulta->where(function ($q) use ($buscar) {
                $like = '%'.addcslashes($buscar, '%_\\').'%';
                $q->where('asunto', 'like', $like)
                    ->orWhere('destinatario', 'like', $like)
                    ->orWhere('nombre', 'like', $like);
            });
        }

        if (in_array($request->query('estado'), [SentMail::ENVIADO, SentMail::FALLIDO], true)) {
            $consulta->where('estado', $request->query('estado'));
        }

        $totales = $usuario->sentMails()
            ->selectRaw('count(*) as total')
            ->selectRaw("sum(case when estado = 'enviado' then 1 else 0 end) as enviados")
            ->selectRaw("sum(case when estado = 'fallido' then 1 else 0 end) as fallidos")
            ->first();

        return view('historial.index', [
            'correos' => $consulta->paginate(10)->withQueryString(),
            'total' => (int) $totales->total,
            'enviados' => (int) $totales->enviados,
            'fallidos' => (int) $totales->fallidos,
        ]);
    }

    public function show(Request $request, SentMail $sentMail)
    {
        abort_unless($sentMail->user_id === $request->user()->id, 404);

        return view('historial.show', ['correo' => $sentMail]);
    }

    public function destroy(Request $request, SentMail $sentMail): RedirectResponse
    {
        abort_unless($sentMail->user_id === $request->user()->id, 404);

        $sentMail->delete();

        return redirect()->route('historial.index')->with('success', 'El registro se eliminó del historial.');
    }
}
