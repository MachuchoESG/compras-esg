<?php

namespace App\Livewire\Component;

use App\Models\Evidencia;
use App\Models\Requisicion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Symfony\Component\CssSelector\Node\FunctionNode;

class Menurequisicion extends Component
{
    use LivewireAlert;

    public $requisicion;
    public $requisicionBorrar;
    public $esjefe = false;
    public $escompras = false;


    public function mount($requisicion)
    {
        $user = User::find(Auth::id());

        $this->esjefe = $user->jefe();
        $this->escompras = $user->compras();
        $this->requisicion = $requisicion;
    }
    public function download($id)
    {
        $evidencia = Evidencia::findOrFail($id);

        if (!$evidencia) {
            $this->alert('warning', 'Evidencia', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'No se encontró el documento',
            ]);
        } else {
            try {
                $path = storage_path('app/' . $evidencia->url);
                return response()->download($path);
            } catch (\Throwable $th) {
                $this->alert('warning', 'Evidencia', [
                    'position' => 'top-end',
                    'timer' => '4000',
                    'toast' => true,
                    'text' => 'No se encontró el documento',
                ]);
            }
        }
    }

    public function setRequisicionBorrar($requi)
    {
        //dd($requi);
        $this->requisicionBorrar = Requisicion::find($requi);

        $this->dispatch('selected-requisicion-borrar', ['requisicion' => $this->requisicionBorrar]);
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
                $this->alert('success', 'La requisicion: ' . $this->requisicion->folio . ' se a Borrado.');
            } catch (\Exception $e) {
                $this->alert('error', 'La requisicion: ' . $this->requisicion->folio . ' no se puede Borrar.');
            }
        } else {
            $this->alert('error', 'La requisicion: ' . $this->requisicion->folio . ' no se puede Borrar.');
        }

        $this->dispatch('cerrar-modal-borrar');
    }

    public function renderRequisiciones()
    {
        $requisiciones = Requisicion::getRequisiciones('', 5);
        return view('livewire.requisicion.index', ['requisiciones' => $requisiciones]);
    }

    public function render()
    {
        return view('livewire.component.menurequisicion');
    }
}
