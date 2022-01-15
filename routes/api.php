<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/tienda/{tienda} ', 'TiendaController@update');




Route::resource('usuario','UsuarioController');
Route::post('/usuario/{usuario} ', 'UsuarioController@update');
Route::post('/usuarioLogin ', 'UsuarioController@login');
Route::post('/usuarioLogOut ', 'UsuarioController@logOut');
Route::post('/storeDuenoDeNegocio ', 'UsuarioController@storeDuenoDeNegocio');
Route::get('/getAllTiendas/{token} ', 'TiendaController@getAllTiendas');
Route::get('/getAllTiendas ', 'TiendaController@getAllTiendas2');
Route::resource('producto','ProductoController');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('register', 'AuthController@register');
    Route::resource('tienda','TiendaController');
    Route::get('/productos/tienda/{tiendaid} ', 'ProductoController@getAllProductosByTiendaId');
    Route::post('/producto/{producto} ', 'ProductoController@update');

});