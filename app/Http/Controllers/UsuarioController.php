<?php

namespace App\Http\Controllers;

use App\Usuario;
use App\Tienda;
use App\Sesiones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
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
            'email'         => 'required|String|email',
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

        $usuario_username=Usuario::where('username',$request->input('username'))->get();

        if(sizeof($usuario_username)>0) return response()->json([ 'created' => false,'errors'=>['El nombre de usuario ya se encuentra registrado']]);
        else{
        $usuario_email=Usuario::where('email',$request->input('email'))->get();
        if(sizeof($usuario_email)>0) return response()->json([ 'created' => false,'errors'=>['El email de usuario ya se encuentra registrado']]);
        }

        if($request->input('is_ucabista') && !strpos($request->input('email'),'ucab.edu.ve'))  return response()->json([ 'created' => false,'errors'=>['El correo ucabista debe de ser valido']]);

        //Fin de las validaciones

    

        if($request->input('is_ucabista')==false)   $is_not_ucabista=true;
        else                                        $is_not_ucabista=false;

        //return $request;
        $usuario = new usuario();
        $usuario->username                  = $request->input('username');
        $usuario->first_name                = $request->input('first_name');
        $usuario->last_name                 = $request->input('last_name');
        $usuario->ruta_imagen_principal     = 'default.jpg';
        $usuario->email                     = $request->input('email');
        $usuario->password                  = md5($request->input('password'));
        $usuario->is_ucabista               = $request->input('is_ucabista');
        $usuario->is_not_ucabista           = !$request->input('is_ucabista');
        $usuario->is_dueño                  = false;
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
        $token=$request->input('token');
        $sesion= Sesiones::where('token',$token)->get();
        if(sizeof($sesion)==0) return response()->json(['message'=>'Inicie sesion primero']);
    
        //INICIO DE LAS VALIDACIONES

        //return $request;

        $rules =  [
            'first_name'    => 'required|String',
            'last_name'     => 'required|String',
            'username'      => 'required|String',
            'email'         => 'required|String',
            'password'      => 'required|String',
            'img.*'         => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];      
       
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'created' => false,
                'errors'  => $validator->errors()->all()
            ]);
        }

    

        $usuario_username=Usuario::where('username',$request->input('username'))->get();

        if(sizeof($usuario_username)>0) return response()->json([ 'created' => false,'errors'=>['El nombre de usuario ya se encuentra registrado']]);
        else{
        $usuario_email=Usuario::where('email',$request->input('email'))->get();
        if(sizeof($usuario_email)>0) return response()->json([ 'created' => false,'errors'=>['El email de usuario ya se encuentra registrado']]);
        }
        
        $usuario=Usuario::findOrFail($id);    

        if($usuario->is_ucabita && !strpos($request->input('email'),'ucab.edu.ve'))  return response()->json([ 'created' => false,'errors'=>['El correo ucabista debe de ser valido']]);
            //Fin de las validaciones

        //imagen_principal
        $image=$request->file('imagen_principal') ;
        $imagen_principal = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_principal);

      

           
        $usuario->username                  = $request->input('username');
        $usuario->first_name                = $request->input('first_name');
        $usuario->last_name                 = $request->input('last_name');
        $usuario->ruta_imagen_principal     = $imagen_principal;
        $usuario->email                     = $request->input('email');
        $usuario->password                  = md5($request->input('password'));
        $usuario->update();

        return response()->json([ 'updated' => true,'message'=>'User Updated Successfully','usuario' => $usuario]);
    }

    public function logOut(){
        session_start();
        if(session_destroy()) return 'Se elimino la sesion exitosamente';
        else return 'Ocurrio un error al eliminar la sesion';
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
        $password=md5($request->input('password'));

        $usuario=Usuario::where(['email'=>$email,'password'=>$password])->get();
        if(sizeof($usuario)==0) return response()->json([ 'login' => false,'errors'=>['No se encontro un usuario con las credenciales']]);
    
        $token=md5($email.$password.date('His'));
        $session= new sesiones();
        $session->token=$token;
        $session->save();
        $usuario->token=$token;

        return response()->json(['login' => true,'session' => $session,'usuario'=>$usuario]);
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
        $usuario->password                  = md5($request->input('password'));
        $usuario->is_ucabista               = false;
        $usuario->is_not_ucabista           = false;
        $usuario->is_dueño                  = true;
        $usuario->save();



        $tienda = new Tienda();
        $tienda->titulo                 = $request->input('titulo');
        $tienda->ruta_imagen_home       = $image_tienda;
        $tienda->ruta_imagen_principal  = $image_tienda;
        $tienda->dueno_id               = $usuario->id;
        $tienda->save();

        return response()->json([ 'created' => true,'message'=>'User And Store Registered Successfully','usuario' => $usuario,'tienda'=>$tienda]);
    }

}
