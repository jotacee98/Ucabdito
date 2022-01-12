<?php

namespace App\Http\Controllers;

use App\Usuario;
use App\Tienda;
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //INICIO DE LAS VALIDACIONES
        $rules =  [
            'first_name'    => 'required|String',
            'last_name'     => 'required|String',
            'username'      => 'required|String',
            'email'         => 'required|String',
            'password'      => 'required|String',
            'img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];      

        $validator = Validator::make($request->all(), $rules);
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

        if($request->input('is_ucabista')==false)   $is_not_ucabista=true;
        else                                        $is_not_ucabista=false;

        //return $request;
        $usuario = new usuario();
        $usuario->username                  = $request->input('username');
        $usuario->first_name                = $request->input('first_name');
        $usuario->last_name                 = $request->input('last_name');
        $usuario->ruta_imagen_principal     = 'default.jpg';
        $usuario->email                     = $request->input('email');
        $usuario->password                  = $request->input('password');
        $usuario->is_ucabista               = $request->input('is_ucabista');
        $usuario->is_not_ucabista           = !$request->input('is_ucabista');
        $usuario->is_dueño                  = $request->input('is_dueño');
       // return $usuario;
        $usuario->save();

        return response()->json(['message'=>'User Registered Successfully','usuario' => $usuario]);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


    
        //INICIO DE LAS VALIDACIONES

        //return $request;

        $rules =  [
            'username'      => 'required|String',
            'email'         => 'required|String|email',
            'first_name'    => 'required|String',
            'last_name'     => 'required|String'
        ];      

       
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'created' => false,
                'errors'  => $validator->errors()->all()
            ]);
        }

        $usuario_username=Usuario::where('username',$request->input('username'))->get();
        if(empty($usuario_username)) return response()->json(['message'=>'El nombre de usuario ya se encuentra registrado']);
        
            //Fin de las validaciones

        //imagen_principal
        $image=$request->file('imagen_principal') ;
        $imagen_principal = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_principal);

      

        $usuario=Usuario::findOrFail($id);        
        $usuario->username                = $request->input('username');
        $usuario->first_name                = $request->input('first_name');
        $usuario->last_name                 = $request->input('last_name');
        $usuario->ruta_imagen_principal     = $imagen_principal;
        $usuario->email                     = $request->input('email');
        $usuario->password                  = $request->input('password');
        $usuario->is_ucabista               = $request->input('is_ucabista');
        $usuario->is_not_ucabista           = $request->input('is_not_ucabista');
        $usuario->is_dueño                  = $request->input('is_dueño');
        $usuario->update();

        return response()->json(['message'=>'User Updated Successfully','user'=>$usuario]);
    }

    public function login(Request $request){
        $rules =  [
            'email'       => 'required|String|email',
            'password'    => 'required|String'
        ];      

       
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'created' => false,
                'errors'  => $validator->errors()->all()
            ]);
        }

        $email=$request->input('email');
        $password=$request->input('password');

        $usuario=Usuario::where(['email'=>$email,'password'=>$password])->get();
        return $usuario;
    }

    public function storeDuenoDeNegocio(Request $request)
    {
        //INICIO DE LAS VALIDACIONES
        $rules =  [
            'first_name'    => 'required|String',
            'last_name'     => 'required|String',
            'username'      => 'required|String',
            'email'         => 'required|String',
            'password'      => 'required|String',
            'img.*'         => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'titulo'        => 'required|unique:tiendas',
        ];      

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return [
                'created' => false,
                'errors'  => $validator->errors()->all()
            ];
        }

        $usuario_username=Usuario::where('username',$request->input('username'))
                                    ->get();

        if(sizeof($usuario_username)>0) return response()->json([ 'created' => false,'errors'=>['El nombre de usuario ya se encuentra registrado']]);
        else{
            $usuario_email=Usuario::where('email',$request->input('email'))->get();
            if(sizeof($usuario_email)>0) return response()->json([ 'created' => false,'errors'=>['El email de usuario ya se encuentra registrado']]);
        }
        //Fin de las validaciones

        //imagen_principal
        $image=$request->file('imagen_principal') ;
        $imagen_principal = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_principal);

        $image_tienda=$request->file('imagen_tienda');
        $image_tiendaprincipal = date('His').$image_tienda->getClientOriginalName();
        $image_tienda->move(public_path().'/uploads/', $image_tiendaprincipal);

        //return $request;
        $usuario = new usuario();
        $usuario->username                  = $request->input('username');
        $usuario->first_name                = $request->input('first_name');
        $usuario->last_name                 = $request->input('last_name');
        $usuario->ruta_imagen_principal     = $imagen_principal;
        $usuario->email                     = $request->input('email');
        $usuario->password                  = $request->input('password');
        $usuario->is_ucabista               = false;
        $usuario->is_not_ucabista           = false;
        $usuario->is_dueño                  = true;
        $usuario->save();



        $tienda = new Tienda();
        $tienda->titulo                 = $request->input('titulo');
        $tienda->ruta_imagen_home       = $imagen_principal;
        $tienda->ruta_imagen_principal  = $imagen_principal;
        $tienda->dueno_id               = $usuario->id;
        $tienda->save();

        return response()->json([ 'created' => true,'message'=>'User And Store Registered Successfully','usuario' => $usuario,'tienda'=>$tienda]);
    }

}
