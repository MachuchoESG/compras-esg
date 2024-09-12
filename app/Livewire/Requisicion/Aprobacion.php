<?php

namespace App\Livewire\Requisicion;

use App\Models\autorizacionhistorial;
use App\Models\Requisicion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Aprobacion extends Component
{

    public $requisicion;
    public function mount()
    {
        $this->visto();
    }
    public function visto()
    {


        if ($this->requisicion->visto != true) {
            $this->requisicion->visto = 1;
            $this->requisicion->save();
        }
    }

    public function aprobar()
    {
        if ($this->requisicion) {
            $this->requisicion->estatus_id = 7;
            $this->requisicion->aprobado = 1;
            $this->requisicion->fechaaprobacion = now();
             
            $this->requisicion->save();
        }
        return redirect()->route('requisicion.index');
    }
    public function noaprobar()
    {
        if ($this->requisicion) {
            $this->requisicion->aprobado = 0;
            $this->requisicion->estatus_id = 4;
            $this->requisicion->save();
        }
        return redirect()->route('requisicion.index');
    }

    public function render()
    {
        return view('livewire.requisicion.aprobacion');
    }
}
