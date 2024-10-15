<?php

namespace App\Http\Controllers;

use App\Models\Requisicion;
use App\Service\EnviarWhatsApp;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class requisicionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        return view('requisicion.index');
    }


    /**
     *
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('requisicion.create');
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
    public function show(Requisicion $requisicion)
    {
        return view('requisicion.show', ['requisicion' => $requisicion]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Requisicion $requisicion)
    {
        return view('requisicion.edit', ['requisicion' => $requisicion]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Requisicion $requisicion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Requisicion $requisicion) {}

    public function autorizar(Requisicion $requisicion)
    {
        return view('requisicion.autorizar', ['requisicion' => $requisicion]);
    }

    public function aprobacion(Requisicion $requisicion)
    {
        return view('requisicion.aprobacion', ['requisicion' => $requisicion]);
    }

    public function cotizacionespecial(Requisicion $requisicion)
    {
        return view('requisicion.cotizacionespecial', ['requisicion' => $requisicion]);
    }

    public function formato(Requisicion $requisicion)
    {

        $requisicion->load('sucursal', 'detalleRequisiciones', 'solicitante');

        $pdf = Pdf::loadView('requisicion.formato', ['requisicion' => $requisicion]);

        return $pdf->stream('requisicion-' . $requisicion->folio . 'pdf');
    }
}
