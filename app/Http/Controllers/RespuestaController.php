<?php

namespace App\Http\Controllers;

use App\Events\VotoMarcado;
use App\Pregunta;
use App\Respuesta;
use App\UserAsistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RespuestaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function registrarRespuesta(Request $request){
        $user = Auth::user();
        $pregunta = Pregunta::where('estado', 1)->first();
        $respuesta = Respuesta::where('user_id', $user->id)->where('pregunta_id', $pregunta->id)->exists();
        $asistencia = UserAsistencia::where('user_id', $user->id)->exists();
        if(!$respuesta && $asistencia){
            Respuesta::create(['valor'=>$request->valor, 'pregunta_id'=>$pregunta->id, 'user_id'=>Auth::user()->id]);
            try {
                event(new VotoMarcado("Voto Marcado"));
            }catch (\Exception $e){
                return null;
            }
        }elseif(!$asistencia){
            return response('No ha registrado asistencia', 402);
        }else{
            return response('Usted ya ha efectuado su voto de manera exitosa, por favor espere que se active el próximo ítem de votación.', 402);
        }
    }

    public function historialVotacion(){
        $user = Auth::user();
        $historial = DB::table('respuestas AS r')
            ->select(['p.pregunta', 'r.created_at', 'r.valor'])
            ->join('preguntas AS p', 'p.id', '=','r.pregunta_id')
            ->where('r.user_id', $user->id)
            ->get();
        return $historial;
    }

}
