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





Route::post('/usuarioLogin ', 'UsuarioController@login');
Route::post('/usuarioLogOut ', 'UsuarioController@logOut');
Route::get('/getAllTiendas/{token} ', 'TiendaController@getAllTiendas');
Route::get('/getAllTiendas ', 'TiendaController@getAllTiendas2');

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
    Route::post('/storeDuenoDeNegocio ', 'UsuarioController@storeDuenoDeNegocio');
    Route::post('/usuario/{usuario} ', 'UsuarioController@update');
    Route::post('/tienda/{tienda} ', 'TiendaController@update');
    Route::resource('producto','ProductoController');
    Route::get('/getAllUsers ', 'UsuarioController@getAllUsers');
    Route::resource('usuario','UsuarioController');
    Route::resource('carro','CarroController');
    Route::post('/carro/add ', 'CarroController@add');
    Route::post('/carro/remove ', 'CarroController@remove');
    Route::get('/carro/getByUserId/{userId} ', 'CarroController@getByUserId');
    Route::resource('pedido','PedidoController');
});