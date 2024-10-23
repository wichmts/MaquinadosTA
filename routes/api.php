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
    
    Route::post('documentos/embarque/{categoria}/{id}', 'APIController@cargarDocumentosEmbarque');
    Route::put('documentos/{modelo}/{id}', 'APIController@subirDocumentos');
    Route::delete('documentos/{id}', 'APIController@eliminarDocumento');
    Route::get('usuario', 'APIController@consultarUsuarios');


    Route::get('materiales', 'APIController@obtenerMateriales');
    Route::get('anios', 'APIController@obtenerAnios');
    Route::get('anios/{anio}/clientes', 'APIController@obtenerPorAnio');
    Route::get('clientes/{cliente}/proyectos', 'APIController@obtenerPorCliente');
    Route::get('proyectos/{proyecto}/herramentales', 'APIController@obtenerPorProyecto');
    Route::get('herramentales/{herramental}/componentes', 'APIController@obtenerPorHerramental');
    Route::get('ultimas-notificaciones', 'APIController@ultimasNotificaciones');
    Route::get('hojas/{material_id}', 'APIController@obtenerHojas');
    Route::get('movimientos-hoja/{hoja_id}', 'APIController@obtenerMovimientosHoja');
    
    Route::post('anios', 'APIController@guardarAnio');
    Route::post('clientes/{anio}', 'APIController@guardarCliente');
    Route::post('proyectos/{cliente_id}', 'APIController@guardarProyecto');
    Route::post('herramental/{proyecto_id}', 'APIController@guardarHerramental');
    Route::post('componente/{herramental_id}/compras', 'APIController@guardarComponentesCompras');
    Route::post('componente/{herramental_id}', 'APIController@guardarComponentes');
    Route::post('hoja', 'APIController@guardarHoja');

    Route::put('liberar-herramental-cargar/{herramental_id}', 'APIController@liberarHerramentalCargar');
    // Route::put('liberar-herramental-compras/{herramental_id}', 'APIController@liberarHerramentalCompras');
    
    Route::put('corte/cambio-estatus/{id}', 'APIController@cambiarEstatusCorte');
    Route::put('corte/finalizar/{id}', 'APIController@finalizarCorte');


});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
