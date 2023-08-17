<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pedidos = Pedido::with(['cliente', 'user', 'productos'])->paginate(10);

        return response()->json($pedidos, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /*
        {
            cliente_id:3,
            producto:[
                {producto_id: 5, cantidad: 2},
                {producto_id: 8, cantidad: 1},
                {producto_id: 11, cantidad: 3},
            ]
        }
        [
         
           cliente_id=>3,
           prodcuto=> [
               [producto_id=> 5, cantidad=> 2],
               [producto_id=> 8, cantidad=> 1],
               [producto_id=> 11, cantidad=> 3],
           ]
        
        ]
        */

        $request->validate([
            "cliente_id" => "required",
            "productos" => "required"
        ]);

        DB::beginTransaction();
        try {
            
        //crear el pedido
        $pedido = new Pedido();
        $pedido->fecha = date("Y-m-d H:i:s");
        $pedido->observacion = "Sin Observaciones";
        $pedido->cliente_id = $request->cliente_id;
        $pedido->user_id = Auth::user()->id;
        $pedido->save();

        // asignar los productos al pedido
        $productos = $request->productos;
        foreach ($productos as $prod){
            $producto_id = $prod["producto_id"];
            $cantidad = $prod["cantidad"];

            $pedido->productos()->attach($producto_id,["cantidad"=> $cantidad]);
        }
        DB::commit();
        return response()->json(["message" => "pedido registrado"]);

        } catch (\Exception $e) {
        DB::rollback();
        
        return response()->json(["message" => "Error al Registrar el Pedido", "error" => $e->getMessage()], 422);
        }

        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
