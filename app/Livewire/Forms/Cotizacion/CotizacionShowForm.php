<?php

namespace App\Livewire\Forms\Cotizacion;

use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Requisicion;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CotizacionShowForm extends Form
{

    use LivewireAlert;
    #[Rule([

        'proveedor_id' => 'required',
        'image' => 'required',
        'dias_entrega' => 'required|numeric|min:1',
        'dias_credito' => 'required|numeric|min:1',
        'formapago' => 'required',
        'comentarios'=>'required'
    ], [], [
        'proveedor_id' => 'Proveedor',
        'image' => 'Cotizacion',
        'dias_entrega' => 'Dias Entrega',
        'dias_credito' => 'Dias Credito',
        'formapago' => 'Metodo Pago',
        'comentarios'=>'Comentario'
    ])]

    public $url;
    public $proveedor_id;
    public $proveedor;
    public $requisicion_id;
    public $dias_entrega = 1;
    public $dias_credito = 1;
    public $moneda = 'MXN';
    public $retencion = 0;
    public $formapago;
    public $comentarios = "";
    public $detalleEditar = [
        'id' => "",
        'producto' => "",
        'cantidad' => "",
        'precio' => "",
    ];


    public $openEditarDetalle = false;
    public $openEditProducto = false;
    public $precios = []; //
    public $retenciones = []; //
    public $image;
    public $requisicion;


    // #[On('Actualizardetalle')]
    // public function Actualizar()
    // {
    //     $this->requisicion = $requisicion = Requisicion::with('cotizaciones')->find($this->requisicionId);
    // }
    public function deleteCotizacion($id)
    {
        Cotizacion::destroy($id);
    }

    public function editarDetalle($id)
    {

        $detalle = DetalleCotizacion::find($id);

        $this->detalleEditar['id'] = $detalle->id;
        $this->detalleEditar['producto'] = $detalle->producto;
        $this->detalleEditar['cantidad'] = $detalle->cantidad;
        $this->detalleEditar['precio'] = $detalle->precio;
        $this->detalleEditar['retencion'] = $detalle->retencion;

        $this->openEditarDetalle = true;
    }

    public function updateDetalle()
    {
        $detalle = DetalleCotizacion::find($this->detalleEditar['id']); // Obtén el detalle a actualizar

        if ($detalle) {
            $detalle->cantidad = $this->detalleEditar['cantidad']; // Actualiza la cantidad
            $detalle->precio = $this->detalleEditar['precio']; // Actualiza el precio
            $detalle->save(); // Guarda los cambios
        }

        // Cierra el modal de edición después de actualizar
        $this->openEditarDetalle = false;
        $this->reset('detalleEditar');
    }

    public function downloadCotizacion($id)
    {

        $archivo = Cotizacion::findOrFail($id);

        if (!$archivo) {
            $this->alert('warning', 'Cotización', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'No se encontró el documento',
            ]);
        } else {
            $path = storage_path('app/' . $archivo->url);

            if (Storage::exists($path)) {
                return response()->download($path);
            } else {
                $this->alert('warning', 'Cotización', [
                    'position' => 'top-end',
                    'timer' => '4000',
                    'toast' => true,
                    'text' => 'El documento no existe',
                ]);
            }
        }
    }


    public function guardarCotizacion()
    {

        //dd($this->requisicion->detalleRequisiciones);

        $this->validate();




        if ($this->requisicion && $this->requisicion->exists) {


            if ($this->image) {
                // Creamos la ruta completa de la carpeta donde se guardará la cotización
                $folder = 'requisicion_' . $this->requisicion->folio . '/cotizaciones';

                // Guardamos la cotización dentro de la carpeta cotizaciones
                $this->url = $this->image->store($folder);
            }




            $this->requisicion_id = $this->requisicion->id;

            $cotizacion = Cotizacion::create($this->only(
                'url',
                'proveedor_id',
                'proveedor',
                'requisicion_id',
                'dias_entrega',
                'dias_credito',
                'formapago',
                'comentarios',
                'moneda'
            ));

            // Crear los detalles de la cotización
            $detalleConPrecios = $this->requisicion->detalleRequisiciones->map(function ($detalle) use ($cotizacion) {
                return [
                    "cotizacion_id" => $cotizacion->id,
                    "producto_id" => $detalle->producto_id,
                    "producto" => $detalle->producto,
                    "cantidad" => $detalle->cantidad,
                    "precio" => isset($this->precios[$detalle->id]) ? $this->precios[$detalle->id] : 0,
                    "retencion" => isset($this->retenciones[$detalle->id]) ? $this->retenciones[$detalle->id] : 0,
                ];
            });


            foreach ($detalleConPrecios as $detalle) {
                DetalleCotizacion::create($detalle);
            }
        }


        $this->reset(
            'url',
            'proveedor_id',
            'proveedor',
            'requisicion_id',
            'dias_entrega',
            'dias_credito',
            'formapago',
            'comentarios',
            'precios'
        );
    }
}
