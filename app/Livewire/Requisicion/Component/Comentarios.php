<?php

namespace App\Livewire\Requisicion\Component;

use Livewire\Attributes\Modelable;
use Livewire\Component;


class Comentarios extends Component
{

  

    
    #[Modelable]
    public $openComentarios;

    public $requisicion;    

    public function render()
    {
        return view('livewire.requisicion.component.comentarios');
    }
}
