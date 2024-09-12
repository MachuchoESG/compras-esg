<?php

namespace App\Livewire\Component;

use App\Models\Evidencia;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Menurequisicion extends Component
{
    public $requisicion = [];

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
    public function render()
    {
        return view('livewire.component.menurequisicion');
    }
}
