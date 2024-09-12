<?php

namespace App\Livewire\Requisicion;

use App\Models\Autorizacionhistorial;
use App\Models\Evidencia;
use App\Models\puestojefe;
use App\Models\Requisicion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Session]
    public $search = '';
    public $paginate = 5;

    public function render()
    {

       
        $requisiciones = Requisicion::getRequisiciones($this->search, $this->paginate);
        return view('livewire.requisicion.index', ['requisiciones' => $requisiciones]);

       
    }
    

    public function estatus($requisicion)
    {
        $primerRegistro = Autorizacionhistorial::where('requisicion_id', $requisicion->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($requisicion->estatus_id == 1) {



            if ($primerRegistro) {
                $usuario = User::where('puesto_id', $primerRegistro->user_id)->first(); // O puedes usar ->get() si esperas múltiples resultados
                return 'Pendiente de aprobar por ' . $usuario->name;
            }
        }
        if ($requisicion->estatus_id == 2) {
            if ($primerRegistro) {
                $usuario = User::where('puesto_id', $primerRegistro->user_id)->first(); // O puedes usar ->get() si esperas múltiples resultados
                return 'Pendiente de autorizar por ' . $usuario->name;
            }
        }

        if ($requisicion->estatus_id == 3) {
            if ($primerRegistro) {
                $usuario = User::where('puesto_id', $primerRegistro->user_id)->first(); // O puedes usar ->get() si esperas múltiples resultados
                return 'Pendiente de autorizar por ' . $usuario->name;
            }
        }

        return '';
    }
}
