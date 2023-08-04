<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //http://127.0.0.1:8000/api/producto?page=5&q=tec
        $buscar = isset($request->q)?$request->q : '';
        $limit = isset($request->limit)?$request->limit: 10;

        if($buscar){
            $productos =Producto::orderBy('id','desc')->where('nombre', 'like', '%'.$buscar.'%')->with("categoria")->paginate($limit);
        }else{
            $productos =Producto::orderBy('id','desc')->with("categoria")->paginate($limit);
        }
        return response()->json($productos,200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validar
        $request->validate([
            "nombre" => "required",
            "categoria_id" => "required"
        ]);
        // guardar
        $prod = new Producto();
        $prod->nombre = $request->nombre;
        $prod->precio = $request->precio;
        $prod->stock = $request->stock;
        $prod->categoria_id = $request->categoria_id;
        $prod->descripcion = $request->descripcion;
        $prod->save();

        // responder
        return response()->json(["message" => "Producto registrado"], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $producto = Producto::with('categoria')->findOrFail($id);
        return response()->json(["message"=>$producto]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // validar
        $request->validate([
            "nombre" => "required",
            "categoria_id" => "required"
        ]);
        $prod = Producto::findOrFail($id);
        // guardar
        $prod->nombre = $request->nombre;
        $prod->precio = $request->precio;
        $prod->stock = $request->stock;
        $prod->categoria_id = $request->categoria_id;
        $prod->descripcion = $request->descripcion;
        $prod->update();

        // responder
        return response()->json(["message" => "Producto Actualizado"], 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prod = Producto::findOrFail($id);
        $prod->delete();
        return response()->json(["message" => "Producto Eliminado"], 200);
    }
    public function actualizarImagen(Request $request, $id){
        if($file = $request->file("imagen")){
            $direccion_imagen = time()."-".$file->getClientOriginalName();
            $file->move("imagen/", $direccion_imagen);
            $direccion_imagen = "imagen/". $direccion_imagen;

            $prod = Producto::find($id);
            $prod->imagen = $direccion_imagen;
            $prod->update();
            return response()->json(["message" => "Imagen Producto Actualizado"], 200);
        }
        return response()->json(["message" => "Se require imagen del Producto"]);
    }
}
