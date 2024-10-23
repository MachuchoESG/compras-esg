<?php

namespace App\Livewire\Requisicion;

use App\Models\Autorizacionhistorial;
use App\Models\Evidencia;
use App\Models\puestojefe;
use App\Models\Requisicion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use LivewireAlert;

    #[Session]
    public $search = '';
    public $paginate = 5;
    public $requisicionBorrar;

    public function setValueBorrar($requisicion)
    {
        //dd($requisicion);
        $requiBorrar = Requisicion::find($requisicion['id']);
        //dd($requiBorrar);
        $this->requisicionBorrar = $requiBorrar;
        $this->dispatch('abrir-modal-borrar', ['requisicionborrar' => $this->requisicionBorrar]);
    }

    public function borrarRequisicion()
    {
        if (Auth::id() == 30 || Auth::id() == 33) {
            $requiBorrar = Requisicion::find($this->requisicionBorrar->id);
            if (!$requiBorrar) {
                $this->alert('error', 'La requisicion seleccionada no se encontro en el sistema.');
                return 0;
                $this->dispatch('cerrar-modal-borrar');
            }
            $requiBorrar->borrado = 1;
            $requiBorrar->fecha_borrado = Carbon::now();
            $requiBorrar->user_borrado = Auth::id();

            try {
                $requiBorrar->save();
                $this->alert('success', 'La requisicion: ' . $this->requisicionBorrar->folio . ' se a Borrado.');
            } catch (\Exception $e) {
                $this->alert('error', 'La requisicion: ' . $this->requisicionBorrar->folio . ' no se puede Borrar.');
            }
        } else {
            $this->alert('error', 'La requisicion: ' . $this->requisicionBorrar->folio . ' no se puede Borrar.');
        }

        $this->dispatch('cerrar-modal-borrar');
    }

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
