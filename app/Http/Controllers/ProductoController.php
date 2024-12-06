<?php

namespace App\Http\Controllers;

use App\Models\Requisicion;
use App\Service\ProductoService;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('catalogos.producto.index', ['open' => false]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $resp = [ 'id'=>1, 'text' => $id ];
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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

    public function getProductosParaAsignar(Request $request){
        $requisicionId = $request->input('ri');
        $requisicion = Requisicion::find($requisicionId);
        $productos = ProductoService::ListaProductos($requisicion->sucursal->nomenclatura);

        return response()->json($productos);
    }

    public function asignarIdProducto (Request $request){
        $idProd = $request->id_producto;
        $idDetalle = $request->id_detalle_cotizacion;

        return response()->json([ 'id_producto' => $idProd , 'id_detalle' => $idDetalle ]);
    }
}
