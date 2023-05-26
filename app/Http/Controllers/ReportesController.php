<?php

namespace App\Http\Controllers;
use App\Empresa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function reporteVotacion($id){

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
            ->where('r.pregunta_id', $id)->get();

        foreach ($respuestas as $respuesta){
            $respuesta->votacion_valida = $this->votosValidos($respuesta->votacion_habil);
        }
        $quorum = DB::table('users_asistencias AS ua')
            ->select(DB::raw('FLOOR((afiliados_categoria_a+afiliados_categoria_b)/15)+1 AS votos_habiles'))
            ->join('users AS u', 'u.id', '=', 'ua.user_id')
            ->join('user_empresa AS ue', 'ue.user_id', '=', 'u.id')
            ->join('empresas AS e','e.nit_sin_digito', '=', 'ue.empresa_nit')
            ->get();
        $total_empresas = Empresa::all()->count();
        $numero_votantes = $quorum->count();
        $total_votos_validos = $quorum->sum('votos_habiles');

        $pregunta = new PreguntaController();

        $datos_pregunta = $pregunta->votacionEnCurso($id);
        $total_votos = $datos_pregunta['total']['SI']+$datos_pregunta['total']['NO']+$datos_pregunta['total']['BLANCO'];
        $porcentaje_votos = [
            'P_VOTOS_SI'=>$total_votos > 0 ?round(($datos_pregunta['total']['SI']/$total_votos)*100, 2):0,
            'P_VOTOS_NO'=>$total_votos >0 ?round(($datos_pregunta['total']['NO']/$total_votos)*100, 2):0,
            'P_VOTOS_BLANCO'=>$total_votos > 0?round(($datos_pregunta['total']['BLANCO']/$total_votos)*100, 2):0
        ];

       //return view('reportes.votacion', compact('respuestas', 'total_empresas', 'numero_votantes', 'total_votos_validos', 'datos_pregunta'));

        $pdf = \PDF::loadView('reportes.votacion', compact('respuestas', 'total_empresas', 'numero_votantes', 'total_votos_validos', 'datos_pregunta', 'porcentaje_votos'));
        $fecha_actual = Carbon::now()->format('Y_m_d_H_i_s');
        return $pdf->download("reporte_votacion_$fecha_actual.pdf");
    }

    public function votosValidos($votos_habil){
        $quorum = DB::table('users_asistencias AS ua')
            ->select(DB::raw('FLOOR((afiliados_categoria_a+afiliados_categoria_b)/15)+1 AS votos_habiles'))
            ->join('users AS u', 'u.id', '=', 'ua.user_id')
            ->join('user_empresa AS ue', 'ue.user_id', '=', 'u.id')
            ->join('empresas AS e','e.nit_sin_digito', '=', 'ue.empresa_nit')
            ->get();
        $numero_votantes = $quorum->count();
        $total_votos_validos = $quorum->sum('votos_habiles');
        $diez_porciento = $numero_votantes > 1 ? 0.1 * $total_votos_validos: $total_votos_validos;
        return $votos_habil >=  $diez_porciento ? (int) $diez_porciento : $votos_habil;
    }

    public function reporteQuorum(){
        $quorum = new PreguntaController();
        $informacion = $quorum->quorunActual();
        $quorum = DB::table('users_asistencias AS ua')
            ->select(DB::raw('FLOOR((afiliados_categoria_a+afiliados_categoria_b)/15)+1 AS votos_habiles'))
            ->join('users AS u', 'u.id', '=', 'ua.user_id')
            ->join('user_empresa AS ue', 'ue.user_id', '=', 'u.id')
            ->join('empresas AS e','e.nit_sin_digito', '=', 'ue.empresa_nit')
            ->get();
        $total_votos_validos = $quorum->sum('votos_habiles');
        $pdf = \PDF::loadView('reportes.quorum', compact('informacion', 'total_votos_validos'));
        $fecha_actual = Carbon::now()->format('Y_m_d_H_i_s');
        return $pdf->download("reporte_quorum_$fecha_actual.pdf");
    }
}
