<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use App\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized','login'=>'false']);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token'   => $token,
            'token_type'     => 'bearer',
            'expires_in'     => auth()->factory()->getTTL() * 60,
            'login'          => true,
            'user'           => auth()->user()
        ]);
    }

    public function register(Request $request){

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

        $User_username=User::where('username',$request->input('username'))->get();

        if(sizeof($User_username)>0) return response()->json([ 'created' => false,'errors'=>['El nombre de User ya se encuentra registrado']]);
        else{
        $User_email=User::where('email',$request->input('email'))->get();
        if(sizeof($User_email)>0) return response()->json([ 'created' => false,'errors'=>['El email de User ya se encuentra registrado']]);
        }

        if($request->input('is_ucabista') && !strpos($request->input('email'),'ucab.edu.ve'))  return response()->json([ 'created' => false,'errors'=>['El correo ucabista debe de ser valido']]);

        //Fin de las validaciones

    

        if($request->input('is_ucabista')==false)   $is_not_ucabista=true;
        else                                        $is_not_ucabista=false;

        //return $request;
        $User = new User();
        $User->username                  = $request->input('username');
        $User->first_name                = $request->input('first_name');
        $User->last_name                 = $request->input('last_name');
        $User->ruta_imagen_principal     = 'default.jpg';
        $User->email                     = $request->input('email');
        $User->password                  = bcrypt($request->input('password'));
        $User->is_ucabista               = $request->input('is_ucabista');
        $User->is_not_ucabista           = !$request->input('is_ucabista');
        $User->is_dueño                  = false;
        $User->name                      = 'empty';
        $User->save();

        return response()->json(['message'=>'User Registered Successfully','User' => $User]);
    }
}