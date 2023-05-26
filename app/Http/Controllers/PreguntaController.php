<?php

namespace App\Http\Controllers;

use App\Empresa;
use App\Events\MessageSent;
use App\Events\NewMessage;
use App\Pregunta;
use App\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PreguntaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
        if($request->ajax()){
            $preguntas = Pregunta::all();
            return datatables()->collection($preguntas)->toJson();
        }
        return view('preguntas');
    }

    public function store(Request $request){
        Pregunta::create(['pregunta'=>$request->pregunta, 'estado'=>0]);
    }

    public function preguntaEnCurso(){
        $user = Auth::user();
        $pregunta = Pregunta::where('estado', 1)->first();
        if($pregunta!= null){
            $respuesta = Respuesta::where('pregunta_id', $pregunta->id)->where('user_id', $user->id)->first();
            if($respuesta == null){
                return $pregunta;
            }
        }
        elseif($pregunta == null){
            return response('Por favor espere que se active el ítem de votación.', 402);
        }
        return response('Usted ya ha efectuado su voto de manera exitosa, por favor espere que se active el próximo ítem de votación.', 402);

    }

    public function votacionEnCurso($id_pregunta=null){
        $data['question'] = $id_pregunta != null ? Pregunta::where('id',$id_pregunta)->first() :Pregunta::where('estado',1)->first();
        $respuestas = DB::table('respuestas AS r')
            ->select([
                DB::raw('FLOOR(((e.afiliados_categoria_a + e.afiliados_categoria_b)/15)+1) AS votacion_habil'),
                'r.valor'
            ])
            ->join('user_empresa AS ue','ue.user_id', '=','r.user_id')
            ->join('empresas AS e','ue.empresa_nit','=','e.nit_sin_digito')
            ->where('r.pregunta_id','=',$id_pregunta!=null?$id_pregunta:$data['question']->id)
            ->get();

        foreach ($respuestas as $respuesta){
            $respuesta->votacion_valida = $this->votosValidos($respuesta->votacion_habil);
        }

        $values = ['SI'=>0,'NO'=>0, 'BLANCO'=>0];
        $suma = $respuestas->groupBy('valor')->map(function ($row) {
            return $row->sum('votacion_valida');
        });
        $data['total'] = array_merge($values, $suma->toArray());
        return $data;
    }

    public function dtVotacionEnCurso(){
         $pregunta = Pregunta::where('estado',1)->first();
         $respuestas = DB::table('respuestas AS r')
             ->select([
                 'u.name',
                 'u.email',
                 'e.tipo_id',
                 'e.nit_sin_digito',
                 'e.razon_social',
                 DB::raw('e.afiliados_categoria_a + e.afiliados_categoria_b + e.afiliados_categoria_c AS total_afiliados'),
                 DB::raw('e.afiliados_categoria_a + e.afiliados_categoria_b AS total_afi_subsidio'),
                 DB::raw('FLOOR(((e.afiliados_categoria_a + e.afiliados_categoria_b)/15)+1) AS votacion_habil'),
                 'r.created_at AS fecha_votacion',
                 'r.valor'
             ])
             ->join('user_empresa AS ue','ue.user_id', '=','r.user_id')
             ->join('empresas AS e','ue.empresa_nit','=','e.nit_sin_digito')
             ->join('users AS u', 'u.id', '=', 'r.user_id')
             ->where('r.pregunta_id', $pregunta->id)->get();
         foreach ($respuestas as $respuesta){
            $respuesta->votacion_valida = $this->votosValidos($respuesta->votacion_habil);
         }
        //$respuestas->addSelect(DB::raw("'votacion_valida' as votacion_valida"));
        $votacion = datatables()->of($respuestas)->toJson();
        return $votacion;
    }

    public function cambiarEstado(Request $request){
        Pregunta::where('estado', 1)->update(['estado'=> 0]);
        Pregunta::where('id', $request->id_pregunta)->update(['estado'=> 1]);
        try {
            event(new MessageSent("Pregunta Establecida"));
        }catch (\Exception $e){
            return null;
        }
    }

    public function quorunActual(){
        $emp_tot = Empresa::all()->count();
        $emp_preinscritas = DB::table('user_empresa')->count();
        $emp_presentes = DB::table('users_asistencias AS ua')
            ->join('user_empresa AS ue', 'ue.user_id', '=', 'ua.user_id')
            ->count();
        //dd($emp_preinscritas);
        $data['total_empresas'] = ['valor'=>$emp_tot, 'porcentaje'=>'100'];
        $data['total_25'] = ['valor'=> floor($emp_tot * 0.25), 'porcentaje'=> '25'];
        $data['total_empresas_presentes'] =['valor'=> $emp_presentes, 'porcentaje'=>round(($emp_presentes/$emp_tot)*100, 3)];
        $data['total_registrados'] = ['valor'=>$emp_preinscritas,'porcentaje'=>round(($emp_preinscritas/$emp_tot)*100, 3)];
        return $data;
    }

   private function votosValidos($votos_habil){
        $quorum = DB::table('users_asistencias AS ua')
            ->select(DB::raw('FLOOR((afiliados_categoria_a+afiliados_categoria_b)/15)+1 AS votos_habiles'))
            ->join('users AS u', 'u.id', '=', 'ua.user_id')
            ->join('user_empresa AS ue', 'ue.user_id', '=', 'u.id')
            ->join('empresas AS e','e.nit_sin_digito', '=', 'ue.empresa_nit')
            ->get();
        $numero_votantes = $quorum->count();
        $total_votos_validos = $quorum->sum('votos_habiles');
        $diez_porciento = $numero_votantes > 1 ? 0.1 * $total_votos_validos : $total_votos_validos;
        return $votos_habil >=  $diez_porciento ? (int) $diez_porciento : $votos_habil;
    }


}
