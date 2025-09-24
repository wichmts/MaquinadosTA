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
    
    
    Route::get('tiempos-maquinas', 'APIController@tiemposMaquinas');
    Route::get('tiempos-personal', 'APIController@tiemposPersonal');

    Route::put('ver-notificaciones', 'APIController@verNotificaciones');
    Route::get('ultimas-notificaciones', 'APIController@ultimasNotificaciones');
    Route::post('notificaciones/{id}/atendida', 'APIController@updateAtendida');

    Route::post('solicitud-refaccion/{componente_id}', 'APIController@generarOrdenRefaccion');
    Route::post('solicitud/{componente_id}', 'APIController@registrarSolicitud');
    Route::post('solicitud-herramental/{herramental_id}', 'APIController@registrarSolicitudHerramental');
    Route::get('solicitud/{componente_id}', 'APIController@obtenerSolicitudes');

    Route::post('documentos/embarque/{categoria}/{id}', 'APIController@cargarDocumentosEmbarque');
    Route::put('documentos/{modelo}/{id}', 'APIController@subirDocumentos');
    Route::delete('documentos/{id}', 'APIController@eliminarDocumento');
    Route::get('usuario', 'APIController@consultarUsuarios');

    Route::get('maquinas', 'APIController@obtenerMaquinas');
    Route::get('maquinas/{maquina_id}/componentes', 'APIController@obtenerComponentesMaquina');
    Route::post('maquina', 'APIController@guardarMaquina');
    Route::put('maquina/{id}', 'APIController@editarMaquina');
    Route::put('maquina/costos/{id}', 'APIController@editarCostoMaquina');
    Route::delete('maquina/{id}', 'APIController@eliminarMaquina');
    
    Route::get('puestos', 'APIController@obtenerPuestos');
    Route::post('puesto', 'APIController@guardarPuesto');
    Route::put('puesto/{id}', 'APIController@editarPuesto');
    Route::delete('puesto/{id}', 'APIController@eliminarPuesto');
    


    Route::get('herramentales', 'APIController@obtenerHerramentales');
    Route::get('materiales', 'APIController@obtenerMateriales');
    Route::get('programadores', 'APIController@obtenerProgramadores');
    Route::get('anios', 'APIController@obtenerAnios');
    Route::get('anios/{anio}/clientes', 'APIController@obtenerPorAnio');
    Route::get('clientes/{cliente}/proyectos', 'APIController@obtenerPorCliente');
    
    Route::get('proyectos/{proyecto}/retrasos', 'APIController@retrasosProyecto');
    Route::get('proyectos/{proyecto}/herramentales', 'APIController@obtenerPorProyecto');
    Route::get('proyectos/{proyecto}/finanzas', 'APIController@tiemposFinanzasPY');
    
    Route::get('herramentales/{herramental}/retrasos', 'APIController@retrasosHerramental');
    Route::get('herramentales/{herramental}/finanzas', 'APIController@tiemposFinanzasHR');
    Route::get('herramentales/{herramental}/componentes', 'APIController@obtenerPorHerramental');
    Route::get('herramental/{herramental}/pruebas-diseno', 'APIController@obtenerPruebasDiseno');
    Route::get('herramental/{herramental}/pruebas-proceso', 'APIController@obtenerPruebasProceso');
    Route::get('componente/{componente}', 'APIController@obtenerComponente');
    Route::post('componente/comentario/{componente}', 'APIController@guardarComentarioComponente');
    Route::get('componentes-reutilizables', 'APIController@obtenerComponentesReutilizables');
    Route::post('componentes-reutilizables', 'APIController@guardarComponentesReutilizables');
    Route::get('notificaciones', 'APIController@notificaciones');
    Route::get('hojas/{material_id}', 'APIController@obtenerHojas');
    Route::get('movimientos-hoja/{hoja_id}', 'APIController@obtenerMovimientosHoja');
    
    Route::post('anios', 'APIController@guardarAnio');
    Route::post('clientes/{anio}', 'APIController@guardarCliente');
    Route::post('proyectos/{cliente_id}', 'APIController@guardarProyecto');
    Route::post('herramental/{proyecto_id}', 'APIController@guardarHerramental');
    Route::post('herramental/cargar-vista-explosionada/{herramental_id}', 'APIController@cargarVistaExplosionada');
    Route::put('herramental/{id}/fecha-limite', 'APIController@fechaLimiteHerramental');
    Route::post('herramental/{herramental_id}/{tipo}', 'APIController@actualizarHerramental');
    Route::get('herramental/{herramental_id}', 'APIController@obtenerHerramental');
    
    Route::post('componente/{herramental_id}/temple', 'APIController@guardarComponentesTemple');
    Route::post('componente/{herramental_id}/compras', 'APIController@guardarComponentesCompras');
    Route::post('componente/{herramental_id}', 'APIController@guardarComponentes');
    Route::post('hoja', 'APIController@guardarHoja');
    Route::delete('hoja/{hoja_id}/{estatus}', 'APIController@bajaHoja');
    
    Route::put('refabricacion-componente/{componente_id}', 'APIController@refabricacionComponente');
    Route::put('retrabajo-componente/{componente_id}', 'APIController@retrabajoComponente');
    Route::put('componente/{componente_id}/enrutamiento/{liberar}', 'APIController@guardarComponenteEnrutamiento');
    Route::post('componente/{componente_id}/programacion/{liberar}', 'APIController@guardarComponenteProgramacion');
    Route::put('componente/{componente_id}/refaccion/{band}', 'APIController@componenteRefaccion');
    Route::post('componente/{fabricacion_id}/fabricacion/{liberar}', 'APIController@guardarComponenteFabricacion');
    Route::post('prueba-diseno/{prueba_id}/{liberar}', 'APIController@guardarPruebaDiseno');
    Route::post('prueba-proceso/{prueba_id}/{liberar}', 'APIController@guardarPruebaProceso');

    Route::delete('cancelar-componente-cargar/{componente_id}', 'APIController@cancelarComponenteCargar');
    Route::put('liberar-componente-cargar/{herramental_id}', 'APIController@liberarComponenteCargar');
    Route::put('liberar-herramental-cargar/{herramental_id}', 'APIController@liberarHerramentalCargar');
    Route::put('liberar-herramental-ensamble/{herramental_id}', 'APIController@liberarHerramentalEnsamble');
    Route::put('liberar-herramental-pruebas-diseno/{herramental_id}', 'APIController@liberarHerramentalPruebasDiseno');
    Route::put('liberar-herramental-pruebas-proceso/{herramental_id}', 'APIController@liberarHerramentalPruebasProceso');

    Route::put('fabricacion/cambio-estatus/{id}', 'APIController@cambiarEstatusFabricacion');
    Route::put('programacion/cambio-estatus/{id}', 'APIController@cambiarEstatusProgramacion');
    Route::put('corte/cambio-estatus/{id}', 'APIController@cambiarEstatusCorte');
    Route::put('corte/finalizar/{id}', 'APIController@finalizarCorte');
    Route::put('ensamble/cambio-estatus/{herramental_id}/{componente_id}', 'APIController@cambiarEstatusEnsamble');


    Route::post('registrar-paro/{componente_id}', 'APIController@registrarParo');
    Route::put('eliminar-paro/{componente_id}/{tipo}', 'APIController@eliminarParo');
    Route::get('linea-tiempo/{componente_id}', 'APIController@obtenerLineaTiempoComponente');

    Route::get('avance-hr/{herramental_id}', 'APIController@obtenerAvanceHR');
    Route::post('prueba-diseno/{herramental_id}', 'APIController@generarPruebaDiseno');
    Route::post('prueba-proceso/{herramental_id}', 'APIController@generarPruebaProceso');

    Route::post('orden-trabajo', 'APIController@generarOrdenTrabajo');
    Route::post('orden-trabajo/{id}', 'APIController@editarOrdenTrabajo');
    Route::get('mis-solicitudes-externas', 'APIController@misSolicitudesExternas');
    Route::get('solicitud-externa/{componente_id}', 'APIController@obtenerSolicitudExterna');
    Route::get('solicitud-ensamble/{herramental_id}', 'APIController@obtenerSolicitudesEnsamble');
    Route::get('solicitud-afilado/{componente_id}', 'APIController@obtenerSolicitudAfilado');

    Route::get('unidad-medida', 'APIController@obtenerUnidadDeMedida');

    Route::post('generar-orden-afilado', 'APIController@generarOrdenAfilado');
    Route::post('editar-orden-afilado/{id}', 'APIController@editarOrdenAfilado');
    Route::get('mis-solicitudes-afilado', 'APIController@misSolicitudesAfilado');

    Route::put('solicitud/{id}/atendida', 'APIController@solicitudAtendida');
    Route::put('actualizar-medidas-componente/{id}', 'APIController@actualizarMedidasComponente');

    /*  */
    Route::put('anios/{id}', 'APIController@actualizarAnio');
    Route::put('clientes/{id}', 'APIController@actualizarCliente');
    Route::put('proyectos/{id}', 'APIController@actualizarProyecto');
    
    Route::delete('anios/{id}', 'APIController@eliminarAnio');
    Route::delete('clientes/{id}', 'APIController@eliminarCliente');
    Route::delete('proyectos/{id}', 'APIController@eliminarProyecto');

    Route::get('trabajos-pendientes', 'APIController@trabajosPendientes');
    Route::get('trabajos-pendientes-general', 'APIController@trabajosPendientesGeneral');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
