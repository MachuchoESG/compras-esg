<?php

namespace App\Livewire\Requisicion;

use App\Models\Autorizacionhistorial;
use App\Models\DetalleRequisicion;
use App\Models\Requisicion;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CotizacionEspecial extends Component
{
    use LivewireAlert;

    public $requisicion;
    public $productosRequisicion = [];
    public $observacionEspecial = '';

    public function continuarRequisicion()
    {
        if ($this->observacionEspecial !== '' && strlen($this->observacionEspecial) > 1) {
            $updatedRequisicion = Requisicion::find($this->requisicion->id);
            $updatedRequisicion->observacion_especial = $this->observacionEspecial;
            $updatedRequisicion->estatus_id = 7;
            $updatedRequisicion->save();
            $this->alert('success', 'Se agrego correctamente la observacion especial.');

            $autorizacion = Autorizacionhistorial::create([
                'requisicion_id' => $this->requisicion->id,
                'user_id' => auth()->user()->puesto_id,
                'user_solicita' => auth()->user()->puesto_id,
                'departamento_id' => auth()->user()->departamento->id,
                'autorizado' => false,
                'visto' => false
            ]);
            return redirect()->route('requisicion.index');
        } else {
            $this->alert('error', 'La observacion especial es obligatoria.');
        }
    }

    public function mount()
    {
        //$vistoHistorial = Autorizacionhistorial::where('requisicion_id', '=', $this->requisicion->id)->get();
        //dd($vistoHistorial);


        //dd($this->requisicion);
        if ($this->requisicion->observacion_especial) {
            $this->observacionEspecial = $this->requisicion->observacion_especial;
        }
        $this->productosRequisicion = DetalleRequisicion::where('requisicion_id', '=', $this->requisicion->id)->get();
    }
    public function render()
    {
        return view('livewire.requisicion.cotizacionespecial');
    }
}
