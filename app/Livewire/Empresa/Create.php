<?php

namespace App\Livewire\Empresa;

use App\Models\Empresa;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;

    public $open = false;
    public $nombre;

    public function save()
    {

        Empresa::create([
            'name' => $this->nombre,
        ]);

        $this->reset('nombre');


        $this->alert('success', 'Empresa creada correctamente!');

        $this->open = false;

        $this->dispatch('empresa-created');
    }
    public function render()
    {
        return view('livewire.empresa.create');
    }
}
