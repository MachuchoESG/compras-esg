<?php

namespace App\Livewire\Sucursal;

use App\Models\Empresa;
use App\Models\Sucursal;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{

    use LivewireAlert;
    public $open = false;

    public $empresas = [];

    public $nombre;
    public $empresa_id = '';
    public $nomenclatura;

    public function save()
    {
        Sucursal::create([
            'name' => $this->nombre,
            'nomenclatura' => $this->nomenclatura,
            'empresa_id' => $this->empresa_id
        ]);

        $this->reset('nombre', 'nomenclatura', 'empresa_id');


        $this->alert('success', 'Sucrusal creada correctamente!');

        $this->open = false;
        $this->dispatch('sucursal-created');
    }


    public function mount()
    {
        $this->empresas = Empresa::all();
    }

    public function render()
    {
        return view('livewire.sucursal.create');
    }
}
