<?php

namespace App\Http\Controllers;

use App\User;
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

        $user_username=User::where('username',$request->input('username'))->get();

        if(sizeof($user_username)>0) return response()->json([ 'created' => false,'errors'=>['El nombre de user ya se encuentra registrado']]);
        else{
        $user_email=User::where('email',$request->input('email'))->get();
        if(sizeof($user_email)>0) return response()->json([ 'created' => false,'errors'=>['El email de user ya se encuentra registrado']]);
        }

        if($request->input('is_ucabista') && !strpos($request->input('email'),'ucab.edu.ve'))  return response()->json([ 'created' => false,'errors'=>['El correo ucabista debe de ser valido']]);

        //Fin de las validaciones

    

        if($request->input('is_ucabista')==false)   $is_not_ucabista=true;
        else                                        $is_not_ucabista=false;

        //return $request;
        $user = new user();
        $user->username                  = $request->input('username');
        $user->first_name                = $request->input('first_name');
        $user->last_name                 = $request->input('last_name');
        $user->ruta_imagen_principal     = 'default.jpg';
        $user->email                     = $request->input('email');
        $user->password                  = md5($request->input('password'));
        $user->is_ucabista               = $request->input('is_ucabista');
        $user->is_not_ucabista           = !$request->input('is_ucabista');
        $user->is_dueño                  = false;
        $user->save();

        return response()->json(['message'=>'User Registered Successfully','user' => $user]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        return User::findOrFail($username);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
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

    

        $user_username=User::where('username',$request->input('username'))->get();

        if(sizeof($user_username)>0) return response()->json([ 'created' => false,'errors'=>['El nombre de user ya se encuentra registrado']]);
        else{
        $user_email=User::where('email',$request->input('email'))->get();
        if(sizeof($user_email)>0) return response()->json([ 'created' => false,'errors'=>['El email de user ya se encuentra registrado']]);
        }
        
        $user=User::findOrFail($id);    

        if($user->is_ucabita && !strpos($request->input('email'),'ucab.edu.ve'))  return response()->json([ 'created' => false,'errors'=>['El correo ucabista debe de ser valido']]);
            //Fin de las validaciones

        //imagen_principal
        $image=$request->file('imagen_principal') ;
        $imagen_principal = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_principal);

      

           
        $user->username                  = $request->input('username');
        $user->first_name                = $request->input('first_name');
        $user->last_name                 = $request->input('last_name');
        $user->ruta_imagen_principal     = $imagen_principal;
        $user->email                     = $request->input('email');
        $user->password                  = md5($request->input('password'));
        $user->update();

        return response()->json([ 'updated' => true,'message'=>'User Updated Successfully','user' => $user]);
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

        $user=User::where(['email'=>$email,'password'=>$password])->get();
        if(sizeof($user)==0) return response()->json([ 'login' => false,'errors'=>['No se encontro un user con las credenciales']]);
    
        $token=md5($email.$password.date('His'));
        $session= new sesiones();
        $session->token=$token;
        $session->save();
        $user->token=$token;

        return response()->json(['login' => true,'session' => $session,'user'=>$user]);
    }

    public function storeDuenoDeNegocio(Request $request)
    {
        //return $request;
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
        
        $user_username=User::where('username',$request->input('username'))
                                    ->get();

        if(sizeof($user_username)>0) return response()->json([ 'created' => false,'errors'=>['El nombre de user ya se encuentra registrado']]);
        else{
            $user_email=User::where('email',$request->input('email'))->get();
            if(sizeof($user_email)>0) return response()->json([ 'created' => false,'errors'=>['El email de user ya se encuentra registrado']]);
        }
        //Fin de las validaciones

        //imagen_principal
        $image=$request->file('imagen_principal') ;
        $imagen_principal = date('His').$image->getClientOriginalName();
        $image->move(public_path().'/uploads/', $imagen_principal);

        $image_tienda=$request->file('imagen_tienda');
        $image_tienda_principal = date('His').$image_tienda->getClientOriginalName();
        $image_tienda->move(public_path().'/uploads/', $image_tienda_principal);

        //return $request;
        //return $imagen_principal;
        $user = new user();
        $user->name                      ='empty';
        $user->username                  = $request->input('username');
        $user->first_name                = $request->input('first_name');
        $user->last_name                 = $request->input('last_name');
        $user->ruta_imagen_principal     = $imagen_principal;
        $user->email                     = $request->input('email');
        $user->password                  = bcrypt($request->input('password'));
        $user->is_ucabista               = false;
        $user->is_not_ucabista           = false;
        $user->is_dueño                  = true;
        $user->save();



        $tienda = new Tienda();
        $tienda->titulo                 = $request->input('titulo');
        $tienda->ruta_imagen_home       = $image_tienda_principal;
        $tienda->ruta_imagen_principal  = $image_tienda_principal;
        $tienda->dueno_id               = $user->id;
        $tienda->save();

        return response()->json([ 'created' => true,'message'=>'User And Store Registered Successfully','user' => $user,'tienda'=>$tienda]);
    }

}
