<?php

namespace App\Http\Controllers;

use App\Events\AsistenciaRegistrada;
use App\User;
use App\UserAsistencia;
use Illuminate\Http\Request;

class UserAsistenciaController extends Controller
{
    public function index(){
        return view('asistencias');
    }

    public function dtAsistencias(){
        $asistentes = User::with('empresas');
        $asistentes->whereHas('roles', function($q){
            $q->where('name', 'votante');
        })->get();
        $asistencias = datatables()->of($asistentes)->toJson();
        return $asistencias;
    }

    public function marcarAsistencia(Request $request){
        $asistencia = UserAsistencia::where('user_id', $request->user_id);
        if(!$asistencia->exists()){
            UserAsistencia::create($request->all());
            try {
                event(new AsistenciaRegistrada("Asistencia Registrada"));
            }catch (\Exception $e){
                return null;
            }
        }else{
            $asistencia->delete();
            try {
                event(new AsistenciaRegistrada("Asistencia Registrada"));
            }catch (\Exception $e){
                return null;
            }
        }
    }
}
