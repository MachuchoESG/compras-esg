<?php

namespace App\Livewire\Estatus;

use App\Models\Estatus;
use Livewire\Component;

class Index extends Component
{
    public $estatus;

    public function getAllEstatus (){
        $this->estatus = Estatus::all();
    }

    public function mount (){
        $this->getAllEstatus();
    }

    public function render()
    {
        return view('livewire.estatus.index');
    }
}
