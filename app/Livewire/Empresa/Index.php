<?php

namespace App\Livewire\Empresa;

use App\Models\Empresa;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{

    use LivewireAlert;
    public $empresas = [];
    public $open = false;
    public $nombre;

    #[On('empresa-created')]
    public function updateEmpresaList()
    {
        $this->empresas = Empresa::all();
    }
    public function mount()
    {
        $this->empresas = Empresa::all();
    }

    public function save()
    {

        Empresa::create([
            'name' => $this->nombre,
        ]);

        $this->reset('nombre');


        $this->alert('success', 'Empresa creada correctamente!');

        $this->open = false;
        $this->empresas = Empresa::all();
    }

    public function render()
    {
        return view('livewire.empresa.index');
    }
}
