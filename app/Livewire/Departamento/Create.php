<?php

namespace App\Livewire\Departamento;

use App\Models\Departamento;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;

    public $open = false;
    public $nombre;

    public function save()
    {
        Departamento::create([
            'name' => $this->nombre,
        ]);

        $this->reset('nombre');


        $this->alert('success', 'Departamento creado correctamente!');

        $this->open = false;

        $this->dispatch('departamento-created');
    }
    public function render()
    {
        return view('livewire.departamento.create');
    }
}
