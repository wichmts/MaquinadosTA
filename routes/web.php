<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;



// PAGINA WEB
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('profile.edit');
    } else {
        return redirect()->route('login');
    }
});
Route::get('/terminos-y-condiciones', 'UserController@terminos')->name('terminos');
Route::get('/verificar-cuenta/{token}', 'UserController@verificarCuenta')->name('verificacion');
Route::get('/home', 'HomeController@index')->name('home');

Route::post('/set-language/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'es'])) {
        Session::put('locale', $lang);
        App::setLocale($lang);
    }
    return response()->json(['status' => 'success']);
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {

    // AUXILIAR DE DISENO
    Route::get('/carga-componentes', 'WebController@cargaComponentes');

    // ALMACENISTA
    Route::get('/compra-componentes', 'WebController@compraComponentes');
    Route::get('/almacen-mp', 'WebController@almacenMP');
    Route::get('/corte', 'WebController@corte');
    


    // ADMIN
    Route::get('usuario', 'WebController@usuarios');
    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
    Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
    Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
    Route::get('{page}', ['as' => 'page.index', 'uses' => 'PageController@index']);
    Route::get('/stream/{folder}/{filename}', ['uses' => 'PageController@stream']);
});

