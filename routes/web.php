<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;



// PAGINA WEB
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    } else {
        return redirect()->route('login');
    }
});
Route::get('/debug/upload-settings', function() {
    $phpSettings = [
        'post_max_size' => ini_get('post_max_size'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
    ];

    return response()->json([
        'php_settings' => $phpSettings,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
        'server_max_upload' => $_SERVER['CONTENT_LENGTH'] ?? 'N/A (No hay upload en curso)',
    ]);
});
Route::get('/terminos-y-condiciones', 'UserController@terminos')->name('terminos');
Route::get('/verificar-cuenta/{token}', 'UserController@verificarCuenta')->name('verificacion');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index')->name('home');

Route::post('/set-language/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'es'])) {
        Session::put('locale', $lang);
        App::setLocale($lang);
    }
    return response()->json(['status' => 'success']);
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {

    // MATRICERO
    Route::get('/matricero', 'WebController@matricero');
    Route::get('/matricero/lista-componentes', 'WebController@listaComponentesMatricero');

    // OPERADOR
    Route::get('/visor-operador', 'WebController@visorOperador');

    // PROGRAMADOR
    Route::get('/visor-programador', 'WebController@visorProgramador');
    
    // JEFE DE AREA
    Route::get('/enrutador', 'WebController@enrutador');
    Route::get('/visor-avance-hr', 'WebController@visorAvanceHR');
    Route::get('/visor-pruebas', 'WebController@visorPruebas');

    // PROCESOS
    Route::get('/pruebas-proceso', 'WebController@pruebasProceso');

    // AUXILIAR DE DISENO
    Route::get('/carga-componentes', 'WebController@cargaComponentes');
    Route::get('/orden-trabajo', 'WebController@ordenTrabajo');

    // ALMACENISTA
    Route::get('/componentes-reutilizables', 'WebController@componentesReutilizables');
    Route::get('/compra-componentes', 'WebController@compraComponentes');
    Route::get('/temple', 'WebController@temple');
    Route::get('/almacen-mp', 'WebController@almacenMP');
    Route::get('/corte', 'WebController@corte');
    Route::get('/centro-notificaciones', 'WebController@centroNotificaciones');

    // FINANZAS
    Route::get('finanzas-py', 'WebController@finanzasPY');
    Route::get('finanzas-hr', 'WebController@finanzasHR');
    Route::get('costos-hora', 'WebController@puestos');

    //ADMINISTRADOR DE CARPETAS
    Route::get('/exploradorCarpetas', 'WebController@exploradorCarpetas');
    
    //SOLICITUD EXTERNA
    Route::get('/carga-afilados', 'WebController@cargaAfilados');

    // ADMIN
    Route::get('usuario', 'WebController@usuarios');
    Route::get('maquina', 'WebController@maquinas');
    Route::get('herramentales', 'WebController@herramentales');
    Route::get('tiempos-personal', 'WebController@tiemposPersonal');
    Route::get('tiempos-maquinas', 'WebController@tiemposMaquinas');
    Route::get('visorGeneral', 'WebController@visorGeneral');
    Route::get('medidas', 'WebController@medidas');

    //Routes para el perfil 
    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
    Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
    Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);

    //Ruta para atrapar todas las demas rutas y enviarlas al controlador de paginas
    Route::get('{page}', ['as' => 'page.index', 'uses' => 'PageController@index']);
    Route::get('/stream/{folder}/{filename}', ['uses' => 'PageController@stream']);
    
});

