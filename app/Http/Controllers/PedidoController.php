<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pedido;
use App\Producto;
use App\Carro;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->middleware('auth:api', ['except' => ['']]);
    }
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

        $pedido = new  Pedido();
        $pedido->nombre         =   $request->input('nombre');
        $pedido->usuario_id     =   $request->input('usuario_id');
        $pedido->is_paypal      =   $request->input('is_paypal');
        $pedido->is_pago_movil  =   $request->input('is_pago_movil');
        $pedido->is_efectivo    =   $request->input('is_efectivo');
        $pedido->descripcion    =   $request->input('descripcion');

        


        if($pedido->is_paypal==true) $pedido->correo_paypal=$request->input('correo_paypal');
        if($pedido->is_efectivo==true) {
           //return json_encode(['pedido'=>$pedido]);
            $image=$request->file('imagen_efectivo') ;
            if($image){
                $imagen_principal = date('His').$image->getClientOriginalName();
                $image->move(public_path().'/uploads/', $imagen_principal);
                $pedido->ruta_imagen=$imagen_principal;
            }


          
        }
        if($pedido->is_pago_movil==true) {
            $image=$request->file('imagen_pago_movil') ;
            if($image){
                $imagen_principal = date('His').$image->getClientOriginalName();
                $image->move(public_path().'/uploads/', $imagen_principal);
                $pedido->ruta_imagen=$imagen_principal;
            }
           
        }
        $pedido->save();
        DB::UPDATE('UPDATE carros SET pedido_id=? WHERE usuario_id=? AND pedido_id IS NULL',[$pedido->id,$pedido->usuario_id]);
        $productos= DB::SELECT('SELECT * FROM carros WHERE usuario_id=? AND pedido_id=?',[$pedido->usuario_id,$pedido->id]);
 

        return json_encode(['pedido'=>$pedido,'productos'=>$productos]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $pedido=Pedido::where('id',$id)->get()[0];

        $carro = Carro::where('pedido_id',$pedido->id)->get();
        $productList=array();

        foreach ($carro as $key => $producto_carro) {
           $producto= Producto::where('id',$producto_carro->producto_id)->get();
           $producto[0]['cantidad']=$producto_carro['cantidad'];
           array_push($productList,$producto);
        }

      
        return json_encode(['pedido'=>$pedido,'productos'=>$productList]);

    

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
