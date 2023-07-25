<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function funLogin(Request $request){
        //validar
        $credenciales = $request->validate([
            
            "email" => "required|email",
            "password" => "required"
            
        ]);
        //verificar
        if(!Auth::attempt($credenciales)){
            return response()->json(["mensaje" => "No Autenticado"], 401);
        }
        //generar token
        $usuario = Auth::user();
        $token = $usuario->createToken("token personal")->plainTextToken;
        // reponder
        return response()->json([
            "access_token" => $token,
            "type_token" => "Bearer",
            "usuario" => $usuario 
        ]);
    }
    public function funRegistro(Request $request){
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required",
            "c_password" => "required|same:password"
        ]);
        $usuario = new User;
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password =bcrypt($request->password);
        $usuario->save();

        return response()->json(["message"=> "Usuario Registrado"],201);
    }
    public function funPerfil(){
        return Auth::user();
    }
    public function funSalir(){
        Auth::user()->tokens()->delete();

        return response()->json(["message" => "SALIO"]);
    }
}
