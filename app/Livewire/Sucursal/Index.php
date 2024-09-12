<?php

namespace App\Livewire\Sucursal;

use App\Models\Sucursal;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{

    public $sucursales = [];



    #[On('sucursal-created')]
    public function updateEmpresaList()
    {
        $this->sucursales = Sucursal::with('empresa')->get();
    }

    public function mount()
    {
        $this->sucursales = Sucursal::with('empresa')->get();
    }
    public function render()
    {
        return view('livewire.sucursal.index');
    }
}
