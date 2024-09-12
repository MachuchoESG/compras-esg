<?php

namespace App\Livewire\DetalleRequisicion;

use App\Models\DetalleRequisicion;
use App\Models\Requisicion;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class Edit extends Component
{

    use LivewireAlert;
    public $open = false;
    public $detalleid;
    public $detalle = [];
    public $listadeproductos = [];
    public $requisicion = [];

    public $search = '';


    public $producto = [
        'id_Producto' => '',
        'producto' => ''

    ];



    #[On('AbrirModalEditarDetalle')]
    public function AbrirModal($detalle)
    {
        $this->detalleid = $detalle;
        $this->detalle = DetalleRequisicion::findOrFail($detalle);

        $this->requisicion = Requisicion::findOrFail($this->detalle->requisicion_id)->load('sucursal');

        $this->dispatch('togglemodal');
    }

    public function mount($productos)
    {


        $this->listadeproductos = $productos;
    }

    public function update()
    {
        // Cargar el de0talle de la requisición





        $detalleRequisicion = DetalleRequisicion::findOrFail($this->detalleid);

        // Actualizar los campos de $producto
        $detalleRequisicion->producto_id = $this->producto['id_Producto'];
        $detalleRequisicion->producto = $this->producto['producto'];

        // Guardar los cambios
        $detalleRequisicion->save();

        $this->dispatch('cerrar-modal-edit-producto');
        $this->alert('success', 'Producto editado correctamente');
    }



    public function render()
    {
        return view('livewire.detalle-requisicion.edit');
    }
}
