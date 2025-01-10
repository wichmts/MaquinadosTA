<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// SIN TOKEN
Route::group(['middleware' => 'api'], function () {
    Route::get('download/{folder}/{filename}', 'APIController@download');
    Route::post('usuario', 'APIController@guardarUsuario');
    Route::put('usuario/{id}', 'APIController@editarUsuario');
    Route::delete('usuario/{id}', 'APIController@eliminarUsuario');
});

// Panel
Route::group(['middleware' => 'auth:api'], function () {
    

    Route::put('ver-notificaciones', 'APIController@verNotificaciones');
    Route::get('ultimas-notificaciones', 'APIController@ultimasNotificaciones');

    Route::post('documentos/embarque/{categoria}/{id}', 'APIController@cargarDocumentosEmbarque');
    Route::put('documentos/{modelo}/{id}', 'APIController@subirDocumentos');
    Route::delete('documentos/{id}', 'APIController@eliminarDocumento');
    Route::get('usuario', 'APIController@consultarUsuarios');

    Route::get('maquinas', 'APIController@obtenerMaquinas');
    Route::get('maquinas/{maquina_id}/componentes', 'APIController@obtenerComponentesMaquina');
    Route::post('maquina', 'APIController@guardarMaquina');
    Route::put('maquina/{id}', 'APIController@editarMaquina');
    Route::delete('maquina/{id}', 'APIController@eliminarMaquina');
    
    Route::get('materiales', 'APIController@obtenerMateriales');
    Route::get('programadores', 'APIController@obtenerProgramadores');
    Route::get('anios', 'APIController@obtenerAnios');
    Route::get('anios/{anio}/clientes', 'APIController@obtenerPorAnio');
    Route::get('clientes/{cliente}/proyectos', 'APIController@obtenerPorCliente');
    Route::get('proyectos/{proyecto}/herramentales', 'APIController@obtenerPorProyecto');
    Route::get('herramentales/{herramental}/componentes', 'APIController@obtenerPorHerramental');
    Route::get('componente/{componente}', 'APIController@obtenerComponente');
    Route::get('notificaciones', 'APIController@notificaciones');
    Route::get('hojas/{material_id}', 'APIController@obtenerHojas');
    Route::get('movimientos-hoja/{hoja_id}', 'APIController@obtenerMovimientosHoja');
    
    Route::post('anios', 'APIController@guardarAnio');
    Route::post('clientes/{anio}', 'APIController@guardarCliente');
    Route::post('proyectos/{cliente_id}', 'APIController@guardarProyecto');
    Route::post('herramental/{proyecto_id}', 'APIController@guardarHerramental');
    Route::post('herramental/{herramental_id}/{tipo}', 'APIController@actualizarHerramental');
    Route::get('herramental/{herramental_id}', 'APIController@obtenerHerramental');

    Route::post('componente/{herramental_id}/compras', 'APIController@guardarComponentesCompras');
    Route::post('componente/{herramental_id}', 'APIController@guardarComponentes');
    Route::post('hoja', 'APIController@guardarHoja');
    Route::delete('hoja/{hoja_id}/{estatus}', 'APIController@bajaHoja');

    Route::put('componente/{componente_id}/enrutamiento/{liberar}', 'APIController@guardarComponenteEnrutamiento');
    Route::post('componente/{componente_id}/programacion/{liberar}', 'APIController@guardarComponenteProgramacion');
    Route::post('componente/{fabricacion_id}/fabricacion/{liberar}', 'APIController@guardarComponenteFabricacion');

    Route::delete('cancelar-componente-cargar/{componente_id}', 'APIController@cancelarComponenteCargar');
    Route::put('liberar-componente-cargar/{herramental_id}', 'APIController@liberarComponenteCargar');
    Route::put('liberar-herramental-cargar/{herramental_id}', 'APIController@liberarHerramentalCargar');

    Route::put('fabricacion/cambio-estatus/{id}', 'APIController@cambiarEstatusFabricacion');
    Route::put('programacion/cambio-estatus/{id}', 'APIController@cambiarEstatusProgramacion');
    Route::put('corte/cambio-estatus/{id}', 'APIController@cambiarEstatusCorte');
    Route::put('corte/finalizar/{id}', 'APIController@finalizarCorte');

    Route::post('registrar-paro/{componente_id}', 'APIController@registrarParo');
    Route::put('eliminar-paro/{componente_id}/{tipo}', 'APIController@eliminarParo');
    Route::get('linea-tiempo/{componente_id}', 'APIController@obtenerLineaTiempoComponente');

    Route::get('avance-hr/{herramental_id}', 'APIController@obtenerAvanceHR');


});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
