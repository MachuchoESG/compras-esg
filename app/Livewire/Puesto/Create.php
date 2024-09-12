<?php

namespace App\Livewire\Puesto;

use App\Models\Departamento;
use App\Models\Puesto;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{

    use LivewireAlert;

    public $open = false;
    public $nombre;
    public $departamento_id = '';
    public $departamentos = [];

    public function save()
    {


        $this->validate([
            'nombre' => 'required|unique:puestos,name',
            'departamento_id' => 'required'
        ]);

        Puesto::create([
            'name' => $this->nombre,
            'departamento_id' => $this->departamento_id
        ]);

        $this->reset('nombre', 'departamento_id');


        $this->alert('success', 'Puesto creado correctamente!');

        $this->open = false;

        $this->dispatch('puesto-created');
    }

    public function mount()
    {
        $this->departamentos = Departamento::all();
    }
    public function render()
    {
        return view('livewire.puesto.create');
    }
}
