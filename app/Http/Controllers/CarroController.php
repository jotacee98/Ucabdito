<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Carro;
use App\Producto;
use Illuminate\Support\Facades\DB;
class CarroController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function getByUserId($userId){

        $carro=Carro::where('usuario_id',$userId)->get();
        $productList=array();

        foreach ($carro as $key => $producto_carro) {
           $producto= Producto::where('id',$producto_carro->producto_id)->get();
           $producto[0]['cantidad']=$producto_carro['cantidad'];
           array_push($productList,$producto);
        }

        return json_encode(['productos'=>$productList]);
    }

    public function add(Request $request){
        $carro = new Carro();
        $carro->usuario_id=     $request->input('usuario_id');
        $carro->producto_id=    $request->input('producto_id');
        $carro->cantidad= $request->input('cantidad');

        $carro->save();
        return response()->json(['message'=>'Producto registrado exitosamente al carrito']);
    }

    public function remove(Request $request){
        $userId=$request->input('usuario_id');
        $productId= $request->input('producto_id');
        DB::DELETE('DELETE FROM carros WHERE usuario_id=? AND producto_id=?',[$userId,$productId]);
        
        $carro=Carro::where('usuario_id',$userId)->get();
        $productList=array();

        foreach ($carro as $key => $producto_carro) {
           $producto= Producto::where('id',$producto_carro->producto_id)->get();
           array_push($productList,$producto);
        }

        return json_encode(['productos'=>$productList]);
    }

    public function update2(){
        DB::UPDATE('UPDATE carros SET pedido_id=null');
        return json_encode(['message'=>'Se actualizaron los productos']); 
    }
}
