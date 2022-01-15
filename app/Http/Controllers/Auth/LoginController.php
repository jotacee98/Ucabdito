<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    Public function login(){
        $credentials = $this->validate(request(),[
            'email' => 'email|required|string',
            'password' => 'password|required|string'
        ]);

        if(Auth::attempt($credentials))
        {
            return 'Inicio de sesión satisfactorio';
        }

        return back()
            ->withErrors(['email' => trans('auth.failed')])
            ->withInput(request(['email']));

    }

    public function showLoginForm(){
        return response()->json(['message'=>'Inicie Sesion Primero',]);
    }

}
