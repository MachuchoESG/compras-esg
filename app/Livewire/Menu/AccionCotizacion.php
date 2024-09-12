<?php

namespace App\Livewire\Menu;


use Livewire\Attributes\On;
use Livewire\Component;

class AccionCotizacion extends Component
{
    public $detalle;

    #[On('AbrirModalProducto')]
    public function abrirmodal()
    {
        $this->dispatch('AbrirModal');
    }

    public function mount($detalle)
    {
        $this->detalle = $detalle;
    }
    public function render()
    {
        return view('livewire.menu.accion-cotizacion');
    }
}
