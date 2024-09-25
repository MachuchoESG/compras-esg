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
    public $contieneProductoSinRegistrar = false;

    public $comentario;
    public $comentario_preautorizacion;
    public $comentario_cotizacionunica;

    public $cantMinimaCotizaciones = 2;


    public $requisicionId;
    public $proveedores = [];
    public $productos = [];
    public $producto = [];
    public $requisicion;

    public $detalleid;
    public $detalle;





    public function toggleCotizacionUnica($isChecked)
    {
        //dd($this->requisicion->cotizaciones()->count());
        if ($this->requisicion->cotizaciones()->count() > 1) {
            $this->alert('error', 'Las requisiciones con "Cotizacion Unica" deben contener una cotización.');
            $this->esCotizacionUnica = false;
            $this->dispatch('uncheckCotizacionUnica');
            return view('livewire.cotizacion.show');
        }
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
            'comentario' => 'required',
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

        if ($this->esCotizacionUnica && $this->requisicion->cotizaciones()->count() > 1) {
            $this->alert('error', 'Las requisiciones con "Cotizacion Unica" deben contener una cotización.');
            $this->esCotizacionUnica = false;
            $this->dispatch('uncheckCotizacionUnica');
            return view('livewire.cotizacion.show');
        }

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
            $this->requisicion->cotizacion_unica = $this->esCotizacionUnica;
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
        //dd($this->requisicion->sucursal->nomenclatura);
        $this->productos = ProductoService::ListaProductos($this->requisicion->sucursal->nomenclatura);
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

        $this->contieneProductoSinRegistrar = false; // validacion para verifiar que no queden productos_id en 0
        foreach ($this->requisicion->detalleRequisiciones as $dr) {
            if ($dr->producto_id === 0) {
                $this->contieneProductoSinRegistrar = true;
            }
        }


        return view('livewire.cotizacion.show');
    }
    public function save()
    {
        if ($this->esCotizacionUnica) { // valida en caso de que se abra el modal de agregar cotizacion si es Cotizacion Unica
            $this->alert('error', 'No se puede dar de alta nueva cotizacion si se marco "Cotizacion Unica"');
            return view('livewire.cotizacion.show');
        }

        $this->cotizacion->guardarCotizacion();

        $this->alert('success', 'Cotizacion agregada con exito!');

        $this->dispatch('cerrar-modal-add-cotizacion');

        $this->renderSelectProv();
    }


    public function renderSelectProv()
    {
        $this->requisicion = $requisicion = Requisicion::with('cotizaciones')->find($this->requisicionId);

        $this->cotizacion->requisicion =   $this->requisicion;
        $proveedoresAll = ProveedorService::ListaProveedores($requisicion->sucursal->nomenclatura);
        $proveedoresAgregados = Cotizacion::where('requisicion_id', $this->requisicion->id)->get();

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


        foreach ($this->requisicion->detalleRequisiciones as $dr) { // recorre los productos de requi, si uno no existe bloquea registro de cotizacion
            if ($dr->producto_id === 0) {
                $this->contieneProductoSinRegistrar = true;
            }
        }

        $this->cotizacion->requisicion =   $this->requisicion;

        $proveedoresAll = ProveedorService::ListaProveedores($requisicion->sucursal->nomenclatura);
        $proveedoresAgregados = Cotizacion::where('requisicion_id', $this->requisicion->id)->get();

        $proveedoresAgregadosIds = $proveedoresAgregados->pluck('proveedor_id')->toArray();

        // Filtrar proveedores que no están en los proveedores agregados recientes
        foreach ($proveedoresAll as $pa) {
            if (!in_array($pa['cidclienteproveedor'], $proveedoresAgregadosIds)) {
                $this->proveedores[] = $pa;  // Agregar el proveedor a la lista
            }
        }

        if ($requisicion->cotizacion_unica) { // si la requi fue regresada, valida si es cotizacion unica y agrega las validaciones
            $this->esCotizacionUnica = true;
            $this->cantMinimaCotizaciones = 1;
        }

        $this->productos = ProductoService::ListaProductos($requisicion->sucursal->nomenclatura);
    }


    public function render()
    {
        //dd('render');
        return view('livewire.cotizacion.show');
    }
}
