<?php

namespace App\Livewire\Estatus;

use App\Models\Estatus;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Index extends Component
{
    use LivewireAlert;
    public $estatus;
    public $nuevo_estatus;
    public $modalNuevoEstatus = false;
    public $descripcion;

    public function crearNuevoEstatus()
    {
        $n_estatus = Estatus::create([
            'name' => $this->nuevo_estatus
        ]);

        $this->alert('success', 'Nuevo Estatus', [
            'position' => 'center',
            'timer' => '6000',
            'toast' => true,
            'text' => 'Estatus "' . $this->nuevo_estatus . '" agregado correctamente.',
        ]);
        $this->modalNuevoEstatus = false;
    }

    public function getAllEstatus()
    {
        $this->estatus = Estatus::all();
    }

    public function mount()
    {
        $this->getAllEstatus();
    }

    public function render()
    {
        return view('livewire.estatus.index');
    }
}
