<?php

namespace App\Livewire\Requisicion;

use App\Models\DetalleRequisicion;
use Livewire\Component;

class CotizacionEspecial extends Component
{
    public $requisicion;
    public $productosRequisicion = [];

    public function mount()
    {
        //dd($this->requisicion);
        $this->productosRequisicion = DetalleRequisicion::where('requisicion_id', '=', $this->requisicion->id)->get();
    }
    public function render()
    {
        return view('livewire.requisicion.cotizacionespecial');
    }
}
