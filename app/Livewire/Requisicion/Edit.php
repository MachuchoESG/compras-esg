<?php

namespace App\Livewire\Requisicion;



use App\Models\Requisicion;
use App\Models\DetalleRequisicion;
use App\Models\Sucursal;
use App\Service\ProductoService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Edit extends Component
{

    use LivewireAlert;



    public $urlApi;
    public $requisicion;
    public $productos = [];
    public $sucursal;
    public $existencias = 0;
    public $openProductoSR = true;
    public $open = false;



    #[Rule(
        [
            'producto.producto_id' => 'required',
            'producto.cantidad' => 'required|min:1',
            'producto.producto' => 'required',


        ],
        [],
        [
            'producto.producto_id' => 'Producto',
            'producto.cantidad' => 'Cantidad',
            'producto.producto' => 'Producto',


        ]
    )]


    public $producto = [
        'producto_id' => '',
        'cantidad' => '',
        'producto' => '',
        'observaciones' => '',
    ];



    public function finalizarIncompleta(){
        $requisicionincompleta = Requisicion::find($this->requisicion->id);


        if($requisicionincompleta){
            $requisicionincompleta->estatus_id=7;
            $requisicionincompleta->save();

            $this->alert('success', 'Requisicion', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'Actualización correcta' . $requisicionincompleta->folio,
            ]);

            return redirect()->route('requisicion.index');
        }else
        {
            
            $this->alert('error', 'Requisicion', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'No se encontro informacion del documento' . $requisicionincompleta->folio,
            ]);
        }
       
    }

    public function updated($property, $value)
    {



        if ($property == "producto.producto_id") {
            $this->existencias = ProductoService::VerificarExistencia($this->sucursal->nomenclatura, $value);
        }
    }

    public function editProduct($id)
    {
        $detalleEditar = DetalleRequisicion::find($id);

        if ($detalleEditar) {
            $this->producto['id'] = $detalleEditar->id;
            $this->producto['cantidad'] = $detalleEditar->cantidad;
            $this->producto['producto'] = $detalleEditar->producto;
            $this->open = true;
        }
    }

    public function deleteProduct($id)
    {
        $detalleEliminar = DetalleRequisicion::find($id);

        if ($detalleEliminar) {
            $detalleEliminar->delete();

            $this->alert('success', 'Requisicion', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'Se elimino correctamente el producto' . $detalleEliminar->producto,
            ]);
        }
    }

    public function addProducto()
    {


        $this->validate();
        $editarRequisicion = Requisicion::find($this->requisicion->id);

        if ($editarRequisicion) {
            $editarRequisicion->detalleRequisiciones()->create($this->producto);
            $this->alert('success', 'Requisicion', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'Se agrego correctamente el producto',
            ]);

            $this->reset('producto', 'existencias');
        }
    }

    public function mount($requisicion)
    {

        $this->requisicion = $requisicion;

        $this->sucursal = Sucursal::find($this->requisicion->sucursal_id);

        $this->productos = ProductoService::ListaProductos($this->sucursal->nomenclatura);
    }

    public function render()
    {
        return view('livewire.requisicion.edit');
    }
}
