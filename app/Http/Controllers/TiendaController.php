<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tienda;
use App\Usuario;
use App\Producto;

class TiendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return json_encode(Tienda::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   

        //Inicio de las validaciones
        $rules =  [
            'titulo' => 'required|unique:tiendas',
            'dueno_id' => 'required|unique:tiendas',
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


        //Imagen Home
        $image=$request->file('imagen_home') ;
        $imagen_home = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_home);

        //imagen_principal
        $image=$request->file('imagen_principal') ;
        $imagen_principal = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_principal);
      

        $tienda = new Tienda();
        $tienda->titulo                 = $request->input('titulo');
        $tienda->ruta_imagen_home       = $imagen_home;
        $tienda->ruta_imagen_principal  = $imagen_principal;
        $tienda->dueno_id               = $request->input('dueno_id');
        $tienda->save();

        return response()->json(['message'=>'Store Registered Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($tienda_id)
    {
        $tienda=Tienda::findOrFail($tienda_id);
        $dueno_id= $tienda->dueno_id;
        $dueno= Usuario::findOrFail($dueno_id);
        $productos = Producto::where('tienda_id',$tienda_id)->get();
        $data=[
            'tienda' => $tienda,
            'dueno'  => $dueno,
            'productos' => $productos
        ];
        return json_encode($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //return $request;
         //Inicio de las validaciones
         $rules =  [
            'titulo' => 'required|unique:tiendas',
            'dueno_id' => 'required',
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


        //Imagen Home
      /*  $image=$request->file('imagen_home') ;
        $imagen_home = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_home);

        //imagen_principal
        $image=$request->file('imagen_principal') ;
        $imagen_principal = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_principal);*/
      
        $tienda=Tienda::findOrFail($id);
        $tienda->titulo                 = $request->input('titulo');
       // $tienda->ruta_imagen_home       = $imagen_home;
       // $tienda->ruta_imagen_principal  = $imagen_principal;
        $tienda->dueno_id               = $request->input('dueno_id');
        $tienda->update();

        return response()->json(['message'=>'Store Was Updated Successfully','tienda'=>$tienda]);
    }
}
