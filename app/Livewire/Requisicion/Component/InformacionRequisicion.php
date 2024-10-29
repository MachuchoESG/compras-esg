<?php

namespace App\Livewire\Requisicion\Component;

use App\Models\Departamento;
use App\Models\Evidencia;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class InformacionRequisicion extends Component
{

    use LivewireAlert;



    public $requisicion;
    public $archivo;
    public $departamentoAsignado;

    public $openComentarios = false;
    public $productosRequisicion = []; // DESARROLLO

    public function mount()
    {
        $this->departamentoAsignado = Departamento::find($this->requisicion->departamento_especial);
        //dd($this->departamentoAsignado);
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
            $path = storage_path('app/' . $evidencia->url);

            if (Storage::exists($evidencia->url)) {
                return response()->download($path);
            } else {
                $this->alert('warning', 'Evidencia', [
                    'position' => 'top-end',
                    'timer' => '4000',
                    'toast' => true,
                    'text' => 'No se encontró el documento',
                ]);
            }
        }
    }
    public function render()
    {
        return view('livewire.requisicion.component.informacion-requisicion');
    }
}
