<?php

namespace App\Http\Controllers;

use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //INICIO DE LAS VALIDACIONES

        $rules =  [
            'first_name' => 'required|String',
            'last_name' => 'required|String',
            'username' => 'required|String',
            'email' => 'required|String',
            'password' => 'required|String',
            'img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];      

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return [
                'created' => false,
                'errors'  => $validator->errors()->all()
            ];
        }

            //Fin de las validaciones

        //imagen_principal
        $image=$request->file('imagen_principal') ;
        $imagen_principal = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_principal);

        $usuario = new usuario();
        $usuario->first_name                 = $request->input('first_name');
        $usuario->last_name                 = $request->input('last_name');
        $usuario->ruta_imagen_principal  = $imagen_principal;
        $usuario->email                 = $request->input('email');
        $usuario->password                 = $request->input('password');
        $usuario->is_ucabista  = $request->input('is_ucabista');
        $usuario->is_not_ucabista  = $request->input('is_not_ucabista');
        $usuario->is_dueño  = $request->input('is_dueño');
        $usuario->save();

        return response()->json(['message'=>'User Registered Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        return Usuario::findOrFail($username);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $username)
    {
        //INICIO DE LAS VALIDACIONES

        $rules =  [
            'username' => 'required|String',
            'email' => 'required|String',
            'img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];      

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return [
                'created' => false,
                'errors'  => $validator->errors()->all()
            ];
        }

            //Fin de las validaciones

        //imagen_principal
        $image=$request->file('imagen_principal') ;
        $imagen_principal = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_principal);

        $usuario=Usuario::findOrFail($username);        
        $usuario->first_name                 = $request->input('first_name');
        $usuario->last_name                 = $request->input('last_name');
        $usuario->ruta_imagen_principal  = $imagen_principal;
        $usuario->email                 = $request->input('email');
        $usuario->password                 = $request->input('password');
        $usuario->is_ucabista  = $request->input('is_ucabista');
        $usuario->is_not_ucabista  = $request->input('is_not_ucabista');
        $usuario->is_dueño  = $request->input('is_dueño');
        $usuario->update();

        return response()->json(['message'=>'User Updated Successfully']);
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
}
