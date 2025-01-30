<?php

namespace App\Livewire\Cotizacion;

use App\Livewire\Forms\Cotizacion\CotizacionShowForm;
use App\Models\Autorizacionhistorial;
use App\Models\Comentarios;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\DetalleRequisicion;
use App\Models\Divisa;
use App\Models\Requisicion;
use App\Models\User;
use App\Service\ApiUrl;
use App\Service\ProductoService;
use App\Service\ProveedorService;
use Carbon\Carbon;
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
    public $valorPeso = 0;





    public function toggleCotizacionUnica($isChecked)
    {
        if ($this->requisicion->cotizaciones()->count() > 1) {
            $this->alert('error', 'Las requisiciones con "Cotizacion Unica" deben contener una cotización.');
            $this->esCotizacionUnica = false;
            $this->dispatch('uncheckCotizacionUnica');
            return view('livewire.cotizacion.show');
        }
        $this->esCotizacionUnica = $isChecked;

        $requisicion = Requisicion::find($this->requisicion->id);
        $requisicion->cotizacion_unica = $this->esCotizacionUnica;
        $requisicion->save();


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
        /* dd($this->requisicion);
        return 0; */
        //dd('se ejecuta');
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

        if ($this->requisicion) {
            $this->requisicion->estatus_id = 2;
            $this->requisicion->cotizacion_unica = $this->esCotizacionUnica;
            $this->requisicion->save();

            $contieneDiesel = false;
            $contieneProductosDifDiesel = false;

            $cotizacionesRequisicion = Cotizacion::select('id')->where('requisicion_id', '=', $this->requisicion->id)->get();
            $allCotizaciones = [];
            foreach ($cotizacionesRequisicion as $cr) {
                $coti = DetalleCotizacion::select('id', 'cotizacion_id', 'producto_id', 'autorizado')->where('cotizacion_id', '=', $cr->id)->get();
                array_push($allCotizaciones, ["cotizacion_id" => $cr->id, "cotizaciones" => $coti]);
            }

            $index = 0;
            $allProcudots = [];
            $indexProdAdded = [];

            foreach ($allCotizaciones as $AC) {
                foreach ($AC['cotizaciones'] as $producto) {
                    array_push($allProcudots, $producto);
                    if ($producto['autorizado'] == 1) {
                        array_push($indexProdAdded, $index); // En caso de recargar pagina valida los index agregados previamente
                    }
                    $index++;
                }
                $index = 0;
            }
            //dd($allProcudots);
            foreach ($allProcudots as $producto) {
                if ($producto->producto_id === 4155) {
                    $contieneDiesel = true;
                } elseif ($producto->producto_id !== 4155) {
                    $contieneProductosDifDiesel = true;
                }
            }

            //dd($contieneDiesel, $contieneProductosDifDiesel);

            if ($contieneDiesel && !$contieneProductosDifDiesel) {
                $userAlta = User::find($this->requisicion->user_id);
                $autorizacion = Autorizacionhistorial::create([
                    'requisicion_id' => $this->requisicion->id,
                    'user_id' => 5, // puesto quien llega
                    'user_solicita' => $userAlta->puesto_id, // puesto quien pide
                    'departamento_id' => $userAlta->departamento_id, // departamento de la requi
                    'autorizado' => false,
                    'visto' => false
                ]);
            }



            //$this->;


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
        $this->productos = ProductoService::ListaProductos($this->requisicion->sucursal->nomenclatura);
        $this->detalleid = $id;
        $this->cotizacion->openEditProducto = true;

        // $this->detalle = DetalleRequisicion::findOrFail($detalle);

        // $this->requisicion = Requisicion::findOrFail($this->detalle->requisicion_id)->load('sucursal');

        $this->dispatch('togglemodal');
    }


    public function AbrirModalAltaProducto()
    {
        $this->dispatch('AbrirModalAltaProducto');
    }


    public function editarDetalle($id)
    {
        $this->cotizacion->editarDetalle($id);
    }

    public function deleteCotizacion($id)
    {
        $allCOT = Cotizacion::where('requisicion_id', '=', $this->requisicion->id)->get();
        $COT = Cotizacion::find($id);
        //dd($allCOT);
        if ($allCOT->count() === 1 && $this->esCotizacionUnica) {
            $this->alert('error', 'Cotización unica debe contener al menos 1 cotización.');
        } else {
            if ($COT) {
                //$this->cotizacion->deleteCotizacion($id);
                Cotizacion::destroy($id);
                $this->alert('success', 'Cotización se elimino Correctamente.');
            } else {
                $this->alert('error', 'Cotización no se encuentra.');
            }

            $this->renderSelectProv();
        }
        /* if ($COT) {
            //$this->cotizacion->deleteCotizacion($id);
            Cotizacion::destroy($id);
            $this->alert('success', 'Cotizacion se elimino Correctamente.');
        } else {
            $this->alert('error', 'Cotizacion no se encuentra.');
        }

        $this->renderSelectProv(); */
    }

    public function update()
    {
        $this->cotizacion->updateDetalle();
    }

    public function openModalRemoveCotizacion($id)
    {
        //dd($id);
        $cotizacion = Cotizacion::find($id);
        // dd($cotizacion);
    }

    public function updateProducto($producto_id, $producto_name, $detalle_id)
    {
        $detalleRequisicion = DetalleRequisicion::findOrFail($detalle_id);

        // Actualizar los campos de $producto

        $detalleRequisicion->producto_id = $producto_id;
        $detalleRequisicion->producto = $producto_name;

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
        //dd( $this->cotizacion->retenciones);

        $this->cotizacion->requisicion_id = $this->requisicionId;
        //dd($this->cotizacion);

        if ($this->cotizacion->moneda === 'USD') {
            $cambioDivisaActual = Divisa::select('*')->where('moneda', 'USD')->where('created_at', Carbon::now())->orderBy('created_at', 'desc')->first();
            if ($cambioDivisaActual) {
                if ($cambioDivisaActual && Carbon::parse($cambioDivisaActual->created_at)->isSameDay(Carbon::now())) {
                    $this->valorPeso = $cambioDivisaActual->valor;
                } else {
                    $apiKey = env('BANXICO_API_KEY');
                    $url = 'https://www.banxico.org.mx/SieAPIRest/service/v1/series/SF43718/datos/oportuno';
                    try {
                        $response = Http::withHeaders([
                            'Bmx-Token' => $apiKey,
                        ])->get($url);

                        if ($response->successful()) {
                            $dataAPI = $response->json();
                            $dataContent = $dataAPI['bmx']['series'][0]['datos'];

                            $fechaFIX = $dataContent[0]['fecha'];
                            $valueFIX = $dataContent[0]['dato'];
                            $monedaFIX = 'USD';
                            $divisaADD = Divisa::create([
                                'moneda' => $monedaFIX,
                                'fecha_fix' => $fechaFIX,
                                'valor' => $valueFIX,
                            ]);
                            $this->valorPeso = $divisaADD->valor;
                        } else {
                            return [
                                'error' => 'No se pudo obtener la información de Banxico.',
                                'status' => $response->status(),
                            ];
                        }
                    } catch (\Exception $e) {
                        return [
                            'error' => 'Hubo un problema al conectarse con la API.',
                            'message' => $e->getMessage(),
                        ];
                    }
                }
            }
        }

        if ($this->esCotizacionUnica) { // valida en caso de que se abra el modal de agregar cotizacion si es Cotizacion Unica
            $this->alert('error', 'No se puede dar de alta nueva cotizacion si se marco "Cotizacion Unica"');
            return view('livewire.cotizacion.show');
        }



        try {
            $this->cotizacion->guardarCotizacion();
            $this->alert('success', 'Cotizacion agregada con exito!');

            //$this->dispatch('cerrar-modal-add-cotizacion');
            $this->dispatch('cerrar-modal');

            $this->renderSelectProv();
        } catch (\Illuminate\Validation\ValidationException $th) {
            //dd($th);
            $this->dispatch('validate-errors', ['errors' => $th->errors()]);
        }
    }


    public function renderSelectProv()
    {
        $this->proveedores = [];
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
        usort($this->proveedores, function ($a, $b) {
            return strcmp($a['crazonsocial'], $b['crazonsocial']);
        });
        //dd($this->proveedores);
        $this->dispatch('nuevo_proveedores', ['proveedores' => $this->proveedores]);
    }

    public function generarCalculoTotal($detalle)
    {
        //dd($cotizacio_id);
        //$subtotal = $detalle->cantidad * $detalle->precio;
        $subtotal = $detalle->cantidad * $detalle->precio;
        $iva = ($detalle->cantidad * $detalle->precio) * 0.16;
        if ($detalle['retencion'] && $detalle['retencion'] !== null && $detalle['retencion'] > 0) {
            $retencion = ((($detalle['cantidad'] * $detalle['precio'])) * .16) * ($detalle['retencion'] / 100);
        } else {
            $retencion = ((($detalle['cantidad'] * $detalle['precio'])) * .16);
        }

        return $subtotal + $iva - $retencion;
    }

    public function generarCalculoSubtotal($detalle)
    {
        //dd($detalle);
        $subtotal = $detalle->cantidad * $detalle->precio;
        return $subtotal;
    }

    public function generarCalculoIVA($detalle)
    {
        //$cotizacion = Cotizacion::find($id);
        return ($detalle->cantidad * $detalle->precio) * 0.16;
    }

    public function generarCalculoRetencion($detalle)
    {
        //dd($detalle);
        return ((($detalle['cantidad'] * $detalle['precio'])) * 0.16) * ($detalle['retencion'] / 100);
    }

    public function generarTotalCotizacion($detalles = [])
    {
        //dd($detalles); 
        $total = 0;
        foreach ($detalles as $detalle) {
            $subtotal = 0;
            $subtotal = $this->generarCalculoTotal($detalle);
            $total = $total + $subtotal;
        }
        return $total;
    }

    public function mount()
    {

        $divisaPeso = Divisa::select('*')->where('moneda', 'USD')->orderBy('created_at', 'desc')->first();
        //dd($divisaPeso);
        $this->valorPeso = $divisaPeso->valor;
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


        usort($this->proveedores, function ($a, $b) {
            return strcmp($a['crazonsocial'], $b['crazonsocial']);
        });

        if ($requisicion->cotizacion_unica) { // si la requi fue regresada, valida si es cotizacion unica y agrega las validaciones
            $this->esCotizacionUnica = true;
            $this->cantMinimaCotizaciones = 1;
        }

        $this->productos = ProductoService::ListaProductos($requisicion->sucursal->nomenclatura);
        usort($this->productos, function ($a, $b) {
            return strcmp($a['cnombreproducto'], $b['cnombreproducto']);
        });
        //dd($this->requisicion->detalleRequisiciones);

        //dd($this->productos);
    }


    public function render()
    {
        return view('livewire.cotizacion.show');
    }
}
