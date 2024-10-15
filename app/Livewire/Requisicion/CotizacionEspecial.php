<?php

namespace App\Livewire\Requisicion;

use Livewire\Component;

class CotizacionEspecial extends Component
{
    public $requisicion;

    public function mount(){
        
    }
    public function render()
    {
        return view('livewire.requisicion.cotizacionespecial');
    }
}
