<?php

namespace App\Livewire\Requisicion;


use App\Models\Autorizacionhistorial;
use App\Models\Comentarios;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\permisosrequisicion;
use App\Models\Requisicion;
use App\Models\User;
use App\Service\ApiUrl;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use PhpParser\Node\Stmt\TryCatch;

class Autorizar extends Component
{


    use LivewireAlert;
    public $requisicion;
    public $open = false;
    public $openspinner = false;
    public $procesando = false;
    public $comentarioOpen = false;
    public $openCancelacion = false;
    public $comentarioFinal = false;
    public $selectedItems = [];
    public $datosActualizados = [];
    public $urlApi;
    public $jefe = "";
    public $comentario = "";
    public $comentariofinal = "";
    public $cotizacionPrevAutorizada = [];

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
    public function visto()
    {

        $userId = Auth::user()->id;


        $registro = AutorizacionHistorial::where('user_id', auth()->user()->puesto->id)
            ->where('visto', 0)
            ->where('requisicion_id', $this->requisicion->id)
            ->first();

        // Si se encuentra el registro, actualizamos el campo visto a true
        if ($registro) {
            $registro->visto = true;
            $registro->save();
        }
    }

    #[On('Actualizar')]
    public function Actualizar($lista)
    {


        foreach ($lista as $item) {
            $this->datosActualizados[] = [
                "id" => $item['id'],
                "cantidad" => $item['cantidad']
            ];

            // Actualizar detalle
            $detalle = DetalleCotizacion::find($item['id']);
            if ($detalle) {
                $detalle->autorizado = true;
                $detalle->cantidad = $item['cantidad'];
                $detalle->save();  // Utilizar save() para guardar los cambios en el modelo.
            }

            // Actualizar cotizacion
            if ($detalle && $detalle->cotizacion) {
                $cotizacion = $detalle->cotizacion;
                $cotizacion->estatus = true;
                $cotizacion->save();  // Utilizar save() para guardar los cambios en el modelo.


            }
        }
    }

    public function tienespermiso($total)
    {


        $usersolicitante = User::find($this->requisicion->empleado_id);
        $departamento_id = $usersolicitante->departamento->id;

        $user = auth()->user();



        $autorizacion = AutorizacionHistorial::where('requisicion_id', $this->requisicion->id)
            ->where('user_id', $user->puesto->id)
            ->where('user_solicita', $user->puesto->id)
            ->where('departamento_id', $departamento_id)
            ->where('autorizado', 0)
            ->first();

        if ($autorizacion !== null) {

            $permiso = permisosrequisicion::where('PuestoAutorizador_id', $user->puesto->id)
                ->where('departamento_id', $departamento_id)
                ->first();

            if ($permiso->monto < $total) {
                return false;
            } else {
                return true;
            }
        } else {

            $autorizacion = autorizacionhistorial::where('requisicion_id', $this->requisicion->id)
                ->where('user_id', $user->puesto->id)
                ->where('departamento_id', $departamento_id)
                ->where('autorizado', 0)
                ->first();

            // si en null significa que ta tiene otra persona por autorizar
            if ($autorizacion !== null) {

                $permiso = permisosrequisicion::where('PuestoSolicitante_id', $autorizacion->user_solicita)
                    ->where('PuestoAutorizador_id', $user->puesto->id)
                    ->where('departamento_id', $departamento_id)
                    ->first();

                //arreglar para que cuando sea el jefe del gerente buscar que se la pueda autorizar

                if ($permiso->monto < $total) {
                    return false;
                } else {
                    return true;

                    $autorizacion->autorizado = 1;
                    $autorizacion->save();
                }
            } else {



                return false;
            }
        }
    }
    public function obtenerTotalAutorizar()
    {
        $cotizaciones = Cotizacion::where('requisicion_id', $this->requisicion->id)->get();

        $totalrequisicion = 0;
        foreach ($cotizaciones as $cotizacion) {
            $totalrequisicion += $cotizacion->detalleCotizaciones->sum(function ($detalle) {
                if ($detalle->autorizado) {
                    return $detalle->cantidad * $detalle->precio;
                }
                return 0;
            });
        }

        return $totalrequisicion;
    }


    public function updateCantidad($id, $cantidad)
    {

        $detalle = DetalleCotizacion::find($id);

        if ($detalle) {
            $detalle->cantidad = $cantidad;
            $detalle->save();
        }
    }
    public function toggleSelection($id, $check)
    {

        $detalle = DetalleCotizacion::find($id);
        //Validar que otras cotizaciones no tengan autorizadas el mismo producto
        $cotizacionDelDetalle = Cotizacion::where('id', '=', $detalle->cotizacion_id)->first();
        $requisicionID = $cotizacionDelDetalle->requisicion_id;

        $cotizacionesRequisicion = Cotizacion::select('id')->where('requisicion_id', '=', $requisicionID)->get();

        $detallesCotizacionesPorRequi = DetalleCotizacion::whereIn('cotizacion_id', $cotizacionesRequisicion)->get();

        if ($check != false) {
            foreach ($detallesCotizacionesPorRequi as $dc) {
                if ($dc->id != $id) {
                    if ($dc->autorizado == 1 && $dc->producto_id == $detalle->producto_id) {
                        $this->alert('warning', 'Ya existe un producto autorizado con el nombre de: ' . $detalle->producto);
                        $this->dispatch('ProductoYaAutoriado', ['id' => $id]);
                        return 0;
                    }
                }
            }
        }

        if ($detalle) {
            $detalle->autorizado = $check;
            $detalle->save();

            // Obtener la cotización relacionada
            $cotizacion = Cotizacion::find($detalle->cotizacion_id);

            if ($cotizacion) {
                // Verificar si algún detalle está autorizado
                $hayAutorizados = $cotizacion->detalleCotizaciones()->where('autorizado', true)->exists();

                // Actualizar el estado de la cotización basado en los detalles
                if ($hayAutorizados) {
                    $cotizacion->estatus = true;
                } else {
                    $cotizacion->estatus = false;
                }

                $cotizacion->save();
            }
        }


        // $check;
        // $objeto = ['id' => $id];


        // if (!in_array($objeto, $this->selectedItems, true)) {
        //     $this->selectedItems[] = $objeto;
        // } else {
        //     $this->selectedItems = array_values(array_filter($this->selectedItems, function ($item) use ($id) {
        //         return $item['id'] != $id;
        //     }));
        // }
    }

    public function noAutorizar()
    {
        $requisicion = Requisicion::find($this->requisicion->id);

        if ($requisicion) {
            $requisicion->estatus_id = 4;
            $requisicion->fechanoautorizacion = now();
            $requisicion->save();



            $user = auth()->user();

            $autorizacion = autorizacionhistorial::where('requisicion_id', $this->requisicion->id)
                ->where('user_id', $user->puesto->id)
                ->where('autorizado', 0)
                ->first();
            if ($autorizacion) {

                $autorizacion->updated_at = now();
                $autorizacion->save();
            }

            $this->openCancelacion = true;
        }
    }

    public function volverCotizar()
    {
        $registro = AutorizacionHistorial::where('user_id', auth()->user()->puesto->id)
            ->where('requisicion_id', $this->requisicion->id)
            ->first();

        // Si se encuentra el registro, actualizamos el campo visto a true
        if ($registro) {
            $registro->visto = false;
            $registro->save();
        }

        $requisicion = Requisicion::find($this->requisicion->id);

        if ($requisicion) {
            $requisicion->estatus_id = 5;
            $requisicion->save();

            $this->alert('success', 'Se cambio correctamente el estatus a volver a cotizar la requisicion con folio ' . $requisicion->folio);
            return redirect()->route('requisicion.index');
        }
    }

    public function autorizarsiguientenivel()
    {

        //primero actualizo a autorizado mi autorizacion historial y despues inserto al que sigue de autorizar


        $user = auth()->user();
        $registro = AutorizacionHistorial::where('user_id', $user->puesto->id)
            ->where('requisicion_id', $this->requisicion->id)
            ->first();

        // Si se encuentra el registro, actualizar el campo 'autorizado' a true
        if ($registro) {
            $registro->update(['autorizado' => true]);
        }
        //obtener a nuestro siguiente nivel ()

        $usersolicitante = User::find($this->requisicion->empleado_id);
        $departamento_id = $usersolicitante->departamento->id;

        $user = permisosrequisicion::getPuestoSuperiorUsuarioAutenticado($departamento_id);


        //si es null mandar mensaje de que no se tiene un flujo de autorizacion 
        if ($user == null) {

            $this->alert('info', 'Requisición', [
                'position' => 'center',
                'timer' => '6000',
                'toast' => true,
                'text' => '¡No se encontro el autorizador del siguiente nivel , aun no cuentas con uno  ',
            ]);
            return;
        }

        //agregar a quien solicita (el que no pudo autorizar)
        // autorizacionhistorial::create([
        //     'requisicion_id' => $this->requisicion->id,
        //     'user_id' => $user->puesto->id,
        //     'user_solicita' => auth()->user()->puesto->id,
        //     'departamento_id' => auth()->user()->departamento->id,
        //     'autorizado' => false,
        //     'visto' => false
        // ]);

        //cambio 0905
        // $historial = autorizacionhistorial::firstOrCreate([
        //     'requisicion_id' => $this->requisicion->id,
        //     'user_id' => $user->puesto->id,
        //     'user_solicita' => auth()->user()->puesto->id,
        //     'departamento_id' => auth()->user()->departamento->id,
        // ], [
        //     'autorizado' => false,
        //     'visto' => false
        // ]);
        //revisar el flujo del departamento deberia seguir respetando de quien esta solicitando 

        $historial = autorizacionhistorial::firstOrCreate([
            'requisicion_id' => $this->requisicion->id,
            'user_id' => $user->puesto->id,
            'user_solicita' => auth()->user()->puesto->id,
            'departamento_id' =>  $departamento_id,
        ], [
            'autorizado' => false,
            'visto' => false
        ]);



        if (!$historial->wasRecentlyCreated) {



            $this->alert('info', 'Requisición', [
                'position' => 'center',
                'timer' => '6000',
                'toast' => true,
                'text' => '¡Esta requisicion ya se encuentra en proceso de autorizacion  ',
            ]);


            return redirect()->route('requisicion.index');
        } else {
            $this->jefe = $user->name;
            $this->comentarioOpen = true;
        }
    }

    public function exitComentario()
    {
        dd("Saliendo sin comentario");
    }

    public function saveComentario()
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

            return redirect()->route('requisicion.index');

            // Redirigir o hacer lo que necesites después de agregar el comentario
        } else {
            // Manejar el caso de error si es necesario
            // Por ejemplo, mostrar un mensaje de error
            $this->alert('error', 'Error al agregar el comentario');
            return redirect()->route('requisicion.index');
        }
    }

    public function saveComentarioFinal()
    {

        $this->validate([
            'comentariofinal' => 'required',
        ], [], [
            'comentariofinal' => 'Comentario',
        ]);

        // Crear el comentario
        $comentario = Comentarios::create([
            'requisicion_id' => $this->requisicion->id,
            'user_id' => Auth::id(),
            'comentario' => $this->comentarioFinal,
        ]);

        if ($comentario) {
            return redirect()->route('requisicion.index');
        } else {
            $this->alert('error', 'Error al agregar el comentario');
            return redirect()->route('requisicion.index');
        }
    }
    public function generarorden()
    {


        try {
            $cotizaciones = Cotizacion::where('requisicion_id', $this->requisicion->id)->get();

            $alMenosUnaCotizacionActiva = false;

            foreach ($cotizaciones as $cotizacion) {
                if ($cotizacion->estatus == true) {
                    $alMenosUnaCotizacionActiva = true;
                    break; // Termina el bucle si se encuentra una cotización activa
                }
            }

            if (!$alMenosUnaCotizacionActiva) {
                $this->alert('info', 'Requisición', [
                    'position' => 'center',
                    'timer' => '6000',
                    'toast' => true,
                    'text' => '¡Favor de seleccionar al menos una cotizacion!',
                ]);
                return;
            }

            $ordenesCompra = [];

            foreach ($cotizaciones as $cotizacion) {


                if ($cotizacion->estatus != false) {


                    $ComercialDocumento = [
                        "cidclienteproveedor" => $cotizacion->proveedor_id,
                        'cidproyecto' =>   $this->requisicion->proyecto_id ?? 0,
                        "ctextoextrA1" => $this->requisicion->folio,
                        "ctextoextrA2" => $this->requisicion->unidad ?? ''

                    ];

                    // //mandar al api
                    $response = Http::timeout(60)->post($this->urlApi  . $this->requisicion->sucursal->nomenclatura . '/ComercialDocumento', $ComercialDocumento);

                    if ($response->successful()) {
                        $documento = $response->json();
                        $ciddocuemento = $documento['ciddocumento'];
                        $foliooc = $documento['cfolio'];



                        $clientes[] = $cotizacion->proveedor;

                        $ordenesCompra[] = $foliooc;

                        $listaCotizaciones = [];

                        foreach ($cotizacion->detalleCotizaciones as $detalle) {
                            if ($detalle->autorizado != false) {
                                $ComercialMovimiento = [
                                    "ciddocumento" => $ciddocuemento,
                                    "cidproducto" => $detalle->producto_id,
                                    "cunidades" => $detalle->cantidad,
                                    "cprecio" => $detalle->precio
                                ];

                                $listaCotizaciones[] = $ComercialMovimiento;
                            }
                        }

                        //armo la lista de lo que autorizacon por detalle y mando una lista
                        $response = Http::timeout(60)->post($this->urlApi  .  $this->requisicion->sucursal->nomenclatura  . '/ComercialMovimiento', $listaCotizaciones);

                        if ($response->successful()) {
                            $usuariorequisicon = User::find($this->requisicion->empleado_id);

                            $ordencompra = [
                                'folio' => $ciddocuemento,
                                'email' => $this->requisicion->seguimiento ? $usuariorequisicon->email : ""
                            ];

                            // $response = Http::post($this->urlApi  .  $this->requisicion->sucursal->nomenclatura  . '/ComercialMovimiento/EnviarCorreo', $ordencompra);
                            $response = Http::timeout(60)->post($this->urlApi  .  $this->requisicion->sucursal->nomenclatura  . '/ComercialMovimiento/EnviarCorreo', $ordencompra);

                            if ($response->successful()) {
                                $this->alert('success', 'Orden de Compra', [
                                    'position' => 'center',
                                    'timer' => '6000',
                                    'toast' => true,
                                    'text' => '¡La orden de compra con el folio ' . $foliooc . ' se ha creado exitosamente y ha sido enviada por correo al proveedor!',
                                ]);
                            } else {
                                $this->alert('info', 'Orden de Compra', [
                                    'position' => 'center',
                                    'timer' => '6000',
                                    'toast' => true,
                                    'text' => '¡Error en el envío de la orden de compra! Por favor, envía manualmente la OC con el folio ' . $foliooc,
                                ]);
                            }
                        }
                    } else {
                        $this->alert('info', 'Orden de Compra', [
                            'position' => 'center',
                            'timer' => '6000',
                            'toast' => true,
                            'text' => '¡En proceso de creacion de orden de compra ! ',
                        ]);
                    }
                }
            }


            $requisicionupdate = Requisicion::find($this->requisicion->id);

            // Verifica si se encontró una requisición válida
            if ($requisicionupdate) {
                // Concatena las órdenes de compra
                $ordenesCompraConcatenadas = implode(',', $ordenesCompra);

                $clientesconcatenados = implode(',', $clientes);

                // Actualiza el campo 'ordenCompra' de la requisición
                $requisicionupdate->ordenCompra = $ordenesCompraConcatenadas;
                $requisicionupdate->proveedor = $clientesconcatenados;
                $requisicionupdate->estatus_id = 6;

                // Guarda los cambios
                $requisicionupdate->save();
            }

            $this->agregarComentarioFinal();
        } catch (\Throwable $th) {

            $requisicionupdate = Requisicion::find($this->requisicion->id);

            // Verifica si se encontró una requisición válida
            if ($requisicionupdate) {

                $requisicionupdate->estatus_id = 11;


                $requisicionupdate->save();
            }
            $this->agregarComentarioFinal();
        }
        return redirect()->route('requisicion.index');
    }
    public function save()
    {

        $total = $this->obtenerTotalAutorizar();

        if ($this->tienespermiso($total)) {

            $this->generarorden();
        } else {
            $this->autorizarsiguientenivel();
        }



        // $this->dispatch('ObtenerCantidad', lista: $this->selectedItems);
    }

    public $productId;
    public $historialCommpra = [];

    public function abrirModal($productId)
    {
        $this->productId = $productId;
        $this->open = true;

        $response = Http::get($this->urlApi  . $this->requisicion->sucursal->nomenclatura . '/ComercialPrecioCompra/' . $this->productId);
        $this->historialCommpra = $response->json();
    }


    public function agregarComentarioFinal()
    {


        $this->alert('info', 'Deseas agregar un comentario final a la requisicion?', [
            'position' => 'center',
            'timer' => 6000,
            'toast' => true,
            'showCancelButton' => true,
            'cancelButtonText' => 'No',
            'onDismissed' => 'cancelled',
            'showDenyButton' => true,
            'denyButtonText' => 'Si',
            'onDenied' => 'confirmed'
        ]);
    }
    protected $listeners = [
        'confirmed',
        'cancelled'
    ];
    public function confirmed()
    {
        $this->comentarioFinal = true;
    }

    public function cancelled()
    {
        return redirect()->route('requisicion.index');
    }

    public function mount($requisicion)
    {
        // Requisicion::with('detalleRequisiciones', 'cotizaciones.detalleCotizaciones')->find($requisicion->id);

        $this->urlApi = ApiUrl::urlApi();
        $this->requisicion = $requisicion;

        $this->visto();
    }
    public function render()
    {
        return view('livewire.requisicion.autorizar');
    }
}
