<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//rutas auth
Route::prefix('/v1/auth')->group(function (){
    Route::post('/login', [AuthController::class, "funLogin"]);
    Route::post('/register', [AuthController::class, "funRegistro"]);

    Route::middleware('auth:sanctum')->group(function (){
        
        Route::get('/perfil', [AuthController::class, "funPerfil"]);
        Route::post('/logout', [AuthController::class, "funSalir"]);
    });
});

Route::middleware('auth:sanctum')->group(function (){

Route::post("producto/{id}/actualizar-imagen", [ProductoController::class, "actualizarImagen"]);
Route::apiResource("categoria", CategoriaController::class);
Route::apiResource("producto", ProductoController::class);
Route::apiResource("cliente", ClienteController::class);
Route::apiResource("pedido", PedidoController::class);

}); 

Route::get("no-autorizado", function(){
    return ["message" => "No Tienes permiso para acceder a esta pagina"];
})->name("login");