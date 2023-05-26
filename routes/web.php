<?php

use App\Events\WebsocketDemoEvent;
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->to('home');
});

Route::get('/admin', [AdminController::class, 'index']);
Auth::routes(['register'=>false]);

Route::group(['middleware' => ['role:admin|votante']], function () {
    Route::get('pregunta_encurso', 'PreguntaController@preguntaEnCurso');
    Route::get('historial_votacion', 'RespuestaController@historialVotacion');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('registrar_respuesta','RespuestaController@registrarRespuesta');
});



Route::group(['middleware' => ['role:admin']], function () {
    Route::resource('asistencia','UserAsistenciaController');
    Route::get('dt_asistencia','UserAsistenciaController@dtAsistencias');
    Route::resource('preguntas', 'PreguntaController');
    Route::put('cambiar_estado', 'PreguntaController@cambiarEstado');
    Route::resource('registrar', UserController::class);
    Route::get('s2_empresas', 'EmpresaController@selectEmpresas');
    Route::post('marcar_asistencia', 'UserAsistenciaController@marcarAsistencia');
    Route::get('quorun_actual', 'PreguntaController@quorunActual');
    Route::get('votacion_encurso','PreguntaController@votacionEnCurso');
    Route::get('dt_votacion_encurso','PreguntaController@dtVotacionEnCurso');
    Route::get('reporte_votacion/{id?}', 'ReportesController@reporteVotacion');
    Route::get('reporte_quorum', 'ReportesController@reporteQuorum');
    Route::post('reset_password', 'UserController@resetPassword');
    Route::get('votacion', function (){
        return view('votacion');
    });
});



