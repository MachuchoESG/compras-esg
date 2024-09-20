<?php

namespace App\Livewire\Cotizacion;

use App\Livewire\Forms\Cotizacion\CotizacionShowForm;
use App\Models\Comentarios;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\DetalleRequisicion;
use App\Models\Requisicion;
use App\Service\ApiUrl;
use App\Service\ProductoService;
use App\Service\ProveedorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use SebastianBergmann\Type\FalseType;

class Show extends Component
{


    use LivewireAlert;
    use WithFileUploads;


    public CotizacionShowForm $cotizacion;


    public $openIncompleta = false;
    public $openPreAutorizacion = false;
    public $openRemoveCotizacion = false;
    public $openCotizacionUnicaComentario = false;
    public $esCotizacionUnica = false;

    public $comentario;
    public $comentario_preautorizacion;
    public $comentario_cotizacionunica;

    public $cantMinimaCotizaciones = 2;


    public $requisicionId;
    public $proveedores = [];
    public $productos = [];
    public $requisicion;

    public $detalleid;
    public $detalle;





    public function toggleCotizacionUnica($isChecked)
    {
        $this->esCotizacionUnica = $isChecked;
        $this->cantMinimaCotizaciones = $isChecked ? 1 : 2;
    }

    public function liberarRequisicion()
    {

        //validar que tenga al menos una cotizacion


        $requisicion = Requisicion::find($this->requisicion->id);



        if ($requisicion->cotizaciones()->count() > 0) {
            // Al menos una cotización encontrada

            if ($requisicion) {
                $requisicion->estatus_id = 12;
                $requisicion->save();


                $this->alert('success', 'Requisicion', [
                    'position' => 'center',
                    'timer' => '5000',
                    'toast' => true,
                    'text' => 'Requisción liberada correctamente',
                ]);

                return redirect()->route('requisicion.index');
            }
        } else {
            $this->alert('error', 'Requisicion', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => 'Favor de agregar una cotización',
            ]);
        }
    }

    public function liberarRequisicionCotUnica()
    {
        $requisicion = Requisicion::find($this->requisicion->id);
        

        $this->validate([
            'comentario_cotizacionunica' => 'required',
        ], [], [
            'comentario' => 'Comentario',
        ]);

        // Crear el comentario
        $comentario = Comentarios::create([
            'requisicion_id' => $this->requisicion->id,
            'user_id' => Auth::id(),
            'comentario' => $this->comentario_cotizacionunica,
        ]);

        if ($requisicion->cotizaciones()->count() > 0) {
            // Al menos una cotización encontrada

            if ($requisicion) {
                $requisicion->estatus_id = 12;
                $requisicion->cotizacion_unica = $this->esCotizacionUnica;
                $requisicion->save();


                $this->alert('success', 'Requisicion', [
                    'position' => 'center',
                    'timer' => '5000',
                    'toast' => true,
                    'text' => 'Requisción liberada correctamente',
                ]);

                return redirect()->route('requisicion.index');
            }
        } else {
            $this->alert('error', 'Requisicion', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => 'Favor de agregar una cotización',
            ]);
        }
    }

    public function incompleta()
    {
        $this->validate([
            'comentario_preautorizacion' => 'required',
        ], [], [
            'comentario' => 'Comentario',
        ]);

        // Crear el comentario
        $comentario = Comentarios::create([
            'requisicion_id' => $this->requisicion->id,
            'user_id' => Auth::id(),
            'comentario' => $this->comentario,
        ]);

        if ($comentario) {
            // Actualizar el estatus de la requisición si es necesario
            $this->requisicion->estatus_id = 10;
            $this->requisicion->save();

            // Mostrar un mensaje de éxito
            $this->alert('success', 'Comentario agregado con éxito');

            // Reiniciar el campo de comentario
            $this->comentario = '';


            return redirect()->route('requisicion.index');

            // Redirigir o hacer lo que necesites después de agregar el comentario
        } else {
            // Manejar el caso de error si es necesario
            // Por ejemplo, mostrar un mensaje de error
            $this->alert('error', 'Error al agregar el comentario');
        }
    }

    public function download($id)
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


            try {
                $path = storage_path('app/' . $archivo->url);
                return response()->download($path);
            } catch (\Throwable $th) {
                $this->alert('warning', 'Cotización', [
                    'position' => 'top-end',
                    'timer' => '4000',
                    'toast' => true,
                    'text' => 'El documento no existe',
                ]);
            }
        }
    }
    public function autorizarCotizacion()
    {
        $this->validate([
            'comentario_preautorizacion' => 'required',
        ], [], [
            'comentario' => 'Comentario',
        ]);

        // Crear el comentario
        $comentario = Comentarios::create([
            'requisicion_id' => $this->requisicion->id,
            'user_id' => Auth::id(),
            'comentario' => $this->comentario_preautorizacion,
        ]);

        //dd($this->requisicion);
        if ($this->requisicion) {
            $this->requisicion->estatus_id = 2;
            $this->requisicion->save();


            $this->alert('success', 'Requisicion', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'Requisicion Autorizada',
            ]);

            return redirect()->route('requisicion.index');
        }
    }

    // #[On('AbrirModalEditarDetalle')]
    public function AbrirModal($id)
    {
        $this->detalleid = $id;
        $this->cotizacion->openEditProducto = true;

        // $this->detalle = DetalleRequisicion::findOrFail($detalle);

        // $this->requisicion = Requisicion::findOrFail($this->detalle->requisicion_id)->load('sucursal');

        $this->dispatch('togglemodal');
    }


    public function AbrirModalAltaProducto()
    {
        // dd('abri modal alta producto');
        $this->dispatch('AbrirModalAltaProducto');
    }


    public function editarDetalle($id)
    {
        $this->cotizacion->editarDetalle($id);
    }

    public function deleteCotizacion($id)
    {
        //dd($id);
        $COT = Cotizacion::find($id);
        if ($COT) {
            //$this->cotizacion->deleteCotizacion($id);
            Cotizacion::destroy($id);
            $this->alert('success', 'Cotizacion se elimino Correctamente.');
        } else {
            $this->alert('error', 'Cotizacion no se encuentra.');
        }
    }

    public function update()
    {
        $this->cotizacion->updateDetalle();
    }

    public function openModalRemoveCotizacion($id)
    {
        dd($id);
        $cotizacion = Cotizacion::find($id);
        dd($cotizacion);
    }

    public function updateProducto()
    {
        $detalleRequisicion = DetalleRequisicion::findOrFail($this->detalleid);

        // Actualizar los campos de $producto

        $detalleRequisicion->producto_id = $this->producto['id_Producto'];
        $detalleRequisicion->producto = $this->producto['producto'];

        // Guardar los cambios
        $detalleRequisicion->save();

        $this->dispatch('cerrar-modal-edit-producto');
        $this->alert('success', 'Producto editado correctamente');
    }
    public function save()
    {

        $this->cotizacion->guardarCotizacion();

        $this->alert('success', 'Cotizacion agregada con exito!');

        $this->dispatch('cerrar-modal-add-cotizacion');

        $this->renderSelectProv();
    }


    public function renderSelectProv()
    {
        $this->requisicion = $requisicion = Requisicion::with('cotizaciones')->find($this->requisicionId);

        $this->cotizacion->requisicion =   $this->requisicion;
        //$this->proveedores = ProveedorService::ListaProveedores($requisicion->sucursal->nomenclatura);
        $proveedoresAll = ProveedorService::ListaProveedores($requisicion->sucursal->nomenclatura);
        //dd($this->proveedores);
        $proveedoresAgregados = Cotizacion::where('requisicion_id', $this->requisicion->id)->get();
        //dd($proveedoresAll);
        //dd($proveedoresAgregados);
        //$arr = json_decode($proveedoresAgregados);
        $proveedoresAgregadosIds = $proveedoresAgregados->pluck('proveedor_id')->toArray();

        // Filtrar proveedores que no están en los proveedores agregados
        foreach ($proveedoresAll as $pa) {
            if (!in_array($pa['cidclienteproveedor'], $proveedoresAgregadosIds)) {
                $this->proveedores[] = $pa;  // Agregar el proveedor a la lista
            }
        }
    }

    public function mount()
    {


        $this->requisicion = $requisicion = Requisicion::with('cotizaciones')->find($this->requisicionId);

        //dd($this->requisicion->detalleRequisiciones);

        $this->cotizacion->requisicion =   $this->requisicion;

        //$this->proveedores = ProveedorService::ListaProveedores($requisicion->sucursal->nomenclatura);
        $proveedoresAll = ProveedorService::ListaProveedores($requisicion->sucursal->nomenclatura);
        //dd($this->proveedores);
        $proveedoresAgregados = Cotizacion::where('requisicion_id', $this->requisicion->id)->get();
        //dd($proveedoresAll);
        //dd($proveedoresAgregados);
        //$arr = json_decode($proveedoresAgregados);
        $proveedoresAgregadosIds = $proveedoresAgregados->pluck('proveedor_id')->toArray();

        // Filtrar proveedores que no están en los proveedores agregados
        foreach ($proveedoresAll as $pa) {
            if (!in_array($pa['cidclienteproveedor'], $proveedoresAgregadosIds)) {
                $this->proveedores[] = $pa;  // Agregar el proveedor a la lista
            }
        }
        //dd($this->proveedores);

        $this->productos = ProductoService::ListaProductos($requisicion->sucursal->nomenclatura);
    }


    public function render()
    {
        //dd('render');
        return view('livewire.cotizacion.show');
    }
}
