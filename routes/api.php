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

Route::resource('tienda','TiendaController');
Route::post('/tienda/{tienda} ', 'TiendaController@update');


Route::resource('producto','ProductoController');
Route::post('/producto/{producto} ', 'ProductoController@update');

Route::resource('usuario','UsuarioController');
Route::post('/usuario/{usuario} ', 'UsuarioController@update');
Route::post('/usuarioLogin ', 'UsuarioController@login');
Route::post('/usuarioLogOut ', 'UsuarioController@logOut');
Route::post('/storeDuenoDeNegocio ', 'UsuarioController@storeDuenoDeNegocio');
Route::get('/productos/tienda/{tiendaid} ', 'ProductoController@getAllProductosByTiendaId');
Route::get('/getAllTiendas/{token} ', 'TiendaController@getAllTiendas');
Route::get('/getAllTiendas ', 'TiendaController@getAllTiendas2');