<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Producto;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Producto::all();
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
            'titulo' => 'required',
            'tienda_id' => 'required|Integer',
            'cantidad' => 'required|Integer',
            'estado_publicado' => 'required|Integer',
            'descripcion' => 'required|String',
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


        //Imagen Principal
        $image=$request->file('imagen_principal') ;
        $imagen_principal = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_principal);

        $producto = new Producto();
        $producto->titulo                    = $request->input('titulo');
        $producto->ruta_imagen_principal     = $imagen_principal;
        $producto->tienda_id                 = $request->input('tienda_id');
        $producto->cantidad                  = $request->input('cantidad');
        $producto->estado_publicado          = $request->input('estado_publicado');
        $producto->descripcion               = $request->input('descripcion');

        $producto->save();

        return response()->json(['message'=>'Product Registered Successfully','producto'=>$producto]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Producto::findOrFail($id);
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
        //Inicio de las validaciones
        $rules =  [
            'titulo' => 'required',
            'cantidad' => 'required|Integer',
            'estado_publicado' => 'required|Integer',
            'descripcion' => 'required|String',
            //'img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];        

        
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return [
                'created' => false,
                'errors'  => $validator->errors()->all()
            ];
        }

        //Fin de las validaciones


        //Imagen Principal
      /*  $image=$request->file('imagen_principal') ;
        $imagen_principal = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_principal);*/

        $producto=Producto::findOrFail($id);
        $producto->titulo                    = $request->input('titulo');
       // $producto->ruta_imagen_principal     = $imagen_principal;
        //$producto->tienda_id                 = $request->input('tienda_id');
        $producto->cantidad                  = $request->input('cantidad');
        $producto->estado_publicado          = $request->input('estado_publicado');
        $producto->descripcion               = $request->input('descripcion');

        $producto->update();

        return response()->json(['message'=>'Product Was Updated Successfully','producto'=>$producto]);
    }

    public function getAllProductosByTiendaId($tienda_id){
       return json_encode(Producto::where('tienda_id',$tienda_id)->get());
    }
}
