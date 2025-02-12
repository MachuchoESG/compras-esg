<?php

namespace App\Livewire\Requisicion;


use App\Models\Autorizacionhistorial;
use App\Models\Comentarios;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Divisa;
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
use PhpParser\Node\Stmt\Else_;
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
    public $comentarioFinalAutorizar = false;
    public $selectedItems = [];
    public $datosActualizados = [];
    public $urlApi;
    public $jefe = "";
    public $comentario = "";
    public $comentariofinal = "";
    public $comentariofinalautorizar = "";
    public $cotizacionPrevAutorizada = [];
    public $indexProdAdded = [];
    public $totalPermitidoAutorizar = 0;
    public $contieneDiesel = false;
    public $contieneProductoDifDiesel = false;

    public $allDetallesSelected = [];
    public $PreDataOrden = [];

    public $requiCalculos;

    public $subtotal = 0;
    public $IVA = 0;
    public $retencion = 0;
    public $TotalCotizacion = 0;
    public $valorPeso = 0;

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
            //dd($cotizacion);
            $moneda = $cotizacion->moneda;
            $totalrequisicion += $cotizacion->detalleCotizaciones->sum(function ($detalle) use ($moneda) {
                if ($detalle->autorizado) {
                    if ($moneda == 'MXN') {
                        return $detalle->cantidad * $detalle->precio;
                    } else {
                        return $detalle->cantidad * ($detalle->precio * $this->valorPeso);
                    }
                }
                return 0;
            });
        }

        return $totalrequisicion + ($totalrequisicion * 0.16);
    }

    public function updateCantidad($id, $cantidad)
    {

        $detalle = DetalleCotizacion::find($id);

        if ($detalle) {
            $detalle->cantidad = $cantidad;
            $detalle->save();
        }
    }

    public function showDD()
    {
        dd($this->indexProdAdded);
    }

    public function toggleSelection($index, $id, $check)
    {
        $errorExiste = false;
        $allproductos = [];
        $detalle = DetalleCotizacion::find($id);

        $cotizacionDelDetalle = Cotizacion::where('id', '=', $detalle->cotizacion_id)->first();
        $requisicionID = $cotizacionDelDetalle->requisicion_id;

        $cotizacionesRequisicion = Cotizacion::select('id')->where('requisicion_id', '=', $requisicionID)->get();
        $allCotizaciones = [];
        foreach ($cotizacionesRequisicion as $cr) {
            $coti = DetalleCotizacion::select('id', 'producto', 'cotizacion_id', 'producto_id', 'autorizado')->where('cotizacion_id', '=', $cr->id)->get();
            //array_push($allproductos, $coti);
            array_push($allCotizaciones, ["cotizacion_id" => $cr->id, "cotizaciones" => $coti]);
        }

        //dd($allCotizaciones);

        if (!$this->requisicion->cotizacion_unica) {
            if ($check != false) {
                //foreach ($allCotizaciones as $AC) {
                //if ($AC['cotizacion_id'] == $detalle->cotizacion_id) {
                if (!in_array($index, $this->indexProdAdded)) {
                    array_push($this->indexProdAdded, $index);
                    //return 0;
                    //  }

                } else {
                    //foreach ($AC['cotizaciones'] as $cot) {
                    if (in_array($index, $this->indexProdAdded)) {
                        $this->alert('warning', 'Ya existe un producto autorizado  ' . $index + 1 . '. ' . $detalle->producto . ' con otro proveedor');
                        $this->dispatch('ProductoYaAutoriado', ['id' => $id]);
                        $errorExiste = true;
                        //return 0;
                    } else {
                        array_push($this->indexProdAdded, $index);
                        //break;
                    }
                    //}
                }
                //}
            } else {
                $pos = array_search($index, $this->indexProdAdded);
                if ($pos !== false) {
                    unset($this->indexProdAdded[$pos]);
                    $this->indexProdAdded = array_values($this->indexProdAdded); // Reorganizar claves
                }
                /* if ($pos !== false) {
                    unset($this->indexProdAdded[$pos]);
                } */
            }
        }

        if ($detalle && !$errorExiste) {
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

            $cotizacionesRequisicion = Cotizacion::select('id')->where('requisicion_id', '=', $requisicionID)->get();
            foreach ($cotizacionesRequisicion as $cr) {
                $coti = DetalleCotizacion::select('id', 'producto', 'cotizacion_id', 'producto_id', 'autorizado')->where('cotizacion_id', '=', $cr->id)->get();
                //array_push($allproductos, $coti);

                foreach ($coti as $producto) {
                    array_push($allproductos, $producto);
                }
            }
        }


        if (!$errorExiste) {
            $this->allDetallesSelected = [];
            foreach ($allproductos as $allp) {
                if ($allp['autorizado'] === 1) {
                    array_push($this->allDetallesSelected, $allp);
                }
            }
        }

        $this->PreDataOrden = $this->generarPrevOrdenCompra();

        //d($this->indexProdAdded);
        $this->contieneDiesel = false;
        $this->contieneProductoDifDiesel = false;

        foreach ($allproductos as $producto) {
            if ($producto->autorizado == 1) {
                if ($producto->producto_id == 4155) {
                    $this->contieneDiesel = true;
                }
                if ($producto->producto_id != 4155) {
                    $this->contieneProductoDifDiesel = true;
                }
            }
        }
    }

    public function noAutorizar()
    {
        $this->openCancelacion = true;
        /* $requisicion = Requisicion::find($this->requisicion->id);

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
        } */
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

    public function saveComentarioCancelado()
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
        } else {

            $this->alert('error', 'Error al agregar el comentario');
            return redirect()->route('requisicion.index');
        }

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
            $this->alert('success', 'Folio ' . $requisicion->folio . 'cambio a estatus NO AUTORIZADO.');
        }
    }

    public function saveComentario()
    {
        $userLogin = auth()->user();
        //$user = permisosrequisicion::getPuestoSuperiorUsuarioAutenticado($userLogin->departamento_id);
        $userSolictante = User::find($this->requisicion->user_id);
        $permiso = permisosrequisicion::where('PuestoSolicitante_id', $userLogin->puesto_id)
            ->where('departamento_id', $userSolictante->departamento_id)
            ->first();
        //dd($permiso);

        //$userLogin = auth()->user();
        $userAutorizador = User::where('puesto_id', '=', $permiso->PuestoAutorizador_id)->first();
        //dd($userAutorizador);

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

        $historial = autorizacionhistorial::firstOrCreate([
            'requisicion_id' => $this->requisicion->id,
            'user_id' => $userAutorizador->puesto_id,
            'user_solicita' => auth()->user()->puesto->id,
            'departamento_id' =>  $userSolictante->departamento_id,
        ], [
            'autorizado' => false,
            'visto' => false
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

    public function renderTotalProveedor($cotizacion)
    {
        $html = '<p>ESTE ES UN TEXTO</p>';
        return $html;
    }

    public function saveComentarioFinalAutorizar()
    {

        $this->save();
        /* if ($comentario) {
            return redirect()->route('requisicion.index');
        } else {
            $this->alert('error', 'Error al agregar el comentario');
            return redirect()->route('requisicion.index');
        } */
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
                    $response = Http::post($this->urlApi  . $this->requisicion->sucursal->nomenclatura . '/ComercialDocumento', $ComercialDocumento);

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
                        $response = Http::post($this->urlApi  .  $this->requisicion->sucursal->nomenclatura  . '/ComercialMovimiento', $listaCotizaciones);

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

    public function generarordenTEST()
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
                    //$response = Http::post($this->urlApi  . $this->requisicion->sucursal->nomenclatura . '/ComercialDocumento', $ComercialDocumento);

                    if (true/* $response->successful() */) {
                        //$documento = $response->json();
                        $ciddocuemento = '777TEST'; //$documento['ciddocumento'];
                        $foliooc = '000000'; //$documento['cfolio'];



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
                        //$response = Http::post($this->urlApi  .  $this->requisicion->sucursal->nomenclatura  . '/ComercialMovimiento', $listaCotizaciones);

                        if (true/* $response->successful() */) {
                            $usuariorequisicon = User::find($this->requisicion->empleado_id);

                            $ordencompra = [
                                'folio' => $ciddocuemento,
                                'email' => $this->requisicion->seguimiento ? $usuariorequisicon->email : ""
                            ];

                            // $response = Http::post($this->urlApi  .  $this->requisicion->sucursal->nomenclatura  . '/ComercialMovimiento/EnviarCorreo', $ordencompra);
                            //$response = Http::timeout(60)->post($this->urlApi  .  $this->requisicion->sucursal->nomenclatura  . '/ComercialMovimiento/EnviarCorreo', $ordencompra);

                            if (true /* $response->successful() */) {
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
                /* $ordenesCompraConcatenadas = implode(',', $ordenesCompra);

                $clientesconcatenados = implode(',', $clientes); */

                // Actualiza el campo 'ordenCompra' de la requisición
                $requisicionupdate->ordenCompra = '999999'; //$ordenesCompraConcatenadas;
                $requisicionupdate->proveedor = 'PRUEBA TEST PROVEEDOR'; //$clientesconcatenados;
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

    public function continuarAutorizar()
    {
        if ($this->totalPermitidoAutorizar > $this->obtenerTotalAutorizar()) {
            $this->comentarioFinalAutorizar = true;
        } else {
            $user = auth()->user();
            $userSolictante = User::find($this->requisicion->user_id);
            $permiso = permisosrequisicion::where('PuestoSolicitante_id', $user->puesto->id)
                ->where('departamento_id', $userSolictante->departamento_id)
                ->first();

            if ($permiso) {
                //$userLogin = auth()->user();
                $userAutorizador = User::where('puesto_id', '=', $permiso->PuestoAutorizador_id)->first(); //permisosrequisicion::getPuestoSuperiorUsuarioAutenticado($userLogin->departamento_id);
                //dd($userAutorizador);
                //dd($user);
                //si es null mandar mensaje de que no se tiene un flujo de autorizacion 
                if ($userAutorizador == null) {
                    $this->alert('info', 'Requisición', [
                        'position' => 'center',
                        'timer' => '6000',
                        'toast' => true,
                        'text' => '¡No se encontro el autorizador del siguiente nivel , aun no cuentas con uno  ',
                    ]);
                    return;
                }
                $this->jefe = $userAutorizador->name;
                $this->comentarioOpen = true;
            } else {
                $this->alert('warning', 'ALERTA', [
                    'position' => 'center',
                    'toast' => true, 
                    'text' => 'No se encontró Autorizador para el siguiente nivel. Revisar flujo de autorización.'
                ]);
            }
        }
    }

    public function save()
    {
        $this->comentarioFinalAutorizar = false;
        $total = $this->obtenerTotalAutorizar();
        /* dd($this->tienespermiso($total));
        return 0; */
        if ($this->tienespermiso($total)) {
            if ($this->comentariofinalautorizar !== '') {
                $comentario = Comentarios::create([
                    'requisicion_id' => $this->requisicion->id,
                    'user_id' => Auth::id(),
                    'comentario' => $this->comentariofinalautorizar,
                ]);
                $this->comentariofinalautorizar = '';
            }

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

    public function generarCalculoSubtotal($id)
    {
        //dd($cotizacio_id);
        $cotizacion = Cotizacion::find($id);
        //dd($cotizacion->detalleCotizaciones);
        $subtotal = $cotizacion->detalleCotizaciones->sum(function ($detalle) {

            return $detalle->cantidad * $detalle->precio;
        });
        return $subtotal;
    }

    public function generarCalculoIVA($id)
    {
        $cotizacion = Cotizacion::find($id);
        return $cotizacion->detalleCotizaciones->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->precio;
        }) * 0.16;
    }

    public function generarCalculoRetencion($id)
    {
        $cotizacion = Cotizacion::find($id);
        return $cotizacion->detalleCotizaciones->sum(function ($detalle) {
            return (($detalle->cantidad * $detalle->precio) * 0.16) * ($detalle->retencion / 100);
        });
    }

    // calculo datos de tabla
    public function generarCalculoTotalDetalle($detalle, $moneda = 'MXN')
    {
        if ($moneda == 'MXN') {
            $subtotal = $detalle->cantidad * $detalle->precio;
            $iva = ($detalle->cantidad * $detalle->precio) * 0.16;
            $retencion = ((($detalle['cantidad'] * $detalle['precio'])) * .16) * ($detalle['retencion'] / 100);
            return $subtotal + $iva - $retencion;
        } else {
            $subtotal = $detalle->cantidad * ($detalle->precio * $this->valorPeso);
            $iva = ($detalle->cantidad * ($detalle->precio * $this->valorPeso)) * 0.16;
            $retencion = ((($detalle['cantidad'] * ($detalle->precio * $this->valorPeso))) * .16) * ($detalle['retencion'] / 100);
            return $subtotal + $iva - $retencion;
        }
    }


    public function generarCalculoSubtotalDetalle($detalle, $moneda = 'MXN')
    {
        //dd($detalle);
        if ($moneda == 'MXN') {
            $subtotal = $detalle->cantidad * $detalle->precio;
        } else {
            $subtotal = $detalle->cantidad * ($detalle->precio * $this->valorPeso);
        }

        return $subtotal;
    }

    public function generarCalculoIVADetalle($detalle, $moneda = 'MXN')
    {
        //$cotizacion = Cotizacion::find($id);
        if ($moneda == 'MXN') {
            return ($detalle->cantidad * $detalle->precio) * 0.16;
        } else {
            return ($detalle->cantidad * ($detalle->precio * $this->valorPeso)) * 0.16;
        }
    }

    public function generarCalculoRetencionDetalle($detalle,  $moneda = 'MXN')
    {
        //dd($detalle);
        if ($moneda) {
            return ((($detalle['cantidad'] * $detalle['precio'])) * 0.16) * ($detalle['retencion'] / 100);
        } else {
            return ((($detalle['cantidad'] * ($detalle['precio'] * $this->valorPeso))) * 0.16) * ($detalle['retencion'] / 100);
        }
    }
    // FIN CALCULOS TABLA
    //GENERAR TABLA ORDEN COMPRA PREV

    public function generarPrevOrdenCompra()
    {
        $arrIDCotis = [];
        foreach ($this->allDetallesSelected as $ads) {
            array_push($arrIDCotis, $ads['cotizacion_id']);
        }

        $dataPreOrder = [];

        $cotizaciones = Cotizacion::with('detalleCotizaciones')->whereIn('id', $arrIDCotis)->get();
        foreach ($cotizaciones as $coti) {
            $prov = $coti['proveedor'];
            $productos = $coti->detalleCotizaciones;
            $formaPago = $coti['formapago'];
            $moneda = $coti['moneda'];
            $productosAutorizados = [];

            foreach ($productos as $prod) {
                if ($prod['autorizado'] === 1) {
                    array_push($productosAutorizados, ['producto' => $prod['producto'], 'cantidad' => $prod['cantidad'], 'precio' => $prod['precio'], 'retencion' => $prod['retencion']]);
                }
            }

            array_push($dataPreOrder, ['proveedor' => $prov, 'formapago' => $formaPago, 'moneda' => $moneda, 'productos' => $productosAutorizados]);
        }

        return $dataPreOrder;
        //dd($dataPreOrder);
    }

    public function calcularSubtotalPreOrdenProveedor($proveedor)
    {
        $subtotal = 0;
        //dd($proveedor);
        if ($proveedor['moneda'] == 'MXN') {
            foreach ($proveedor['productos'] as $producto) {
                $subtotal = $subtotal + ($producto['cantidad'] * $producto['precio']);
            }
        } else {
            foreach ($proveedor['productos'] as $producto) {
                $subtotal = $subtotal + ($producto['cantidad'] * ($producto['precio'] * $this->valorPeso));
            }
        }


        return number_format($subtotal, 2, '.', ',');
    }

    public function calcularIVAPreOrdenProveedor($proveedor)
    {
        $totalIva = 0;
        if ($proveedor['moneda'] == 'MXN') {
            foreach ($proveedor['productos'] as $producto) {
                $totalIva = $totalIva + (($producto['cantidad'] * $producto['precio']) * 0.16);
            }
        } else {
            foreach ($proveedor['productos'] as $producto) {
                $totalIva = $totalIva + (($producto['cantidad'] * ($producto['precio'] * $this->valorPeso)) * 0.16);
            }
        }

        return number_format($totalIva, 2, '.', ',');
    }

    public function calcularTotalPreOrdenProveedor($proveedor)
    {
        $total = 0;
        $totalIva = 0;
        $retencion = 0;
        if ($proveedor['moneda'] == 'MXN') {
            foreach ($proveedor['productos'] as $producto) {
                $totalIva = $totalIva + (($producto['cantidad'] * $producto['precio']) * 0.16);
                $total = $total + ($producto['cantidad'] * $producto['precio']);
                $retencion = $retencion + ((($producto['cantidad'] * $producto['precio']) * 0.16) * $producto['retencion'] / 100);
            }
        } else {
            foreach ($proveedor['productos'] as $producto) {
                $totalIva = $totalIva + (($producto['cantidad'] * ($producto['precio'] * $this->valorPeso)) * 0.16);
                $total = $total + ($producto['cantidad'] * ($producto['precio'] * $this->valorPeso));
                $retencion = $retencion + ((($producto['cantidad'] * ($producto['precio'] * $this->valorPeso)) * 0.16) * $producto['retencion'] / 100);
            }
        }


        return number_format($totalIva + $total - $retencion, 2, '.', ',');
    }

    public function calcularTotalPagarPreOrdenProveedor($proveedores)
    {
        //dd($proveedores);
        $totalPagar = 0;
        $totalIva = 0;
        $subtotal = 0;
        $retencion = 0;
        foreach ($proveedores as $proveedor) {
            if ($proveedor['moneda'] == 'MXN') {
                foreach ($proveedor['productos'] as $producto) {
                    $totalIva = $totalIva + (($producto['cantidad'] * $producto['precio']) * 0.16);
                    $subtotal = $subtotal + ($producto['cantidad'] * $producto['precio']);
                    $retencion = $retencion + ((($producto['cantidad'] * $producto['precio']) * 0.16) * $producto['retencion'] / 100);
                }
                /*  $totalPagar = ($totalPagar + $totalIva + $subtotal) - $retencion;
                $totalIva = 0;
                $subtotal = 0; */
            } else {
                foreach ($proveedor['productos'] as $producto) {
                    $totalIva = $totalIva + (($producto['cantidad'] * ($producto['precio'] * $this->valorPeso)) * 0.16);
                    $subtotal = $subtotal + ($producto['cantidad'] * ($producto['precio'] * $this->valorPeso));
                    $retencion = $retencion + ((($producto['cantidad'] * ($producto['precio'] * $this->valorPeso)) * 0.16) * $producto['retencion'] / 100);
                }
                /* $totalPagar = ($totalPagar + $totalIva + $subtotal) - $retencion;
                $totalIva = 0;
                $subtotal = 0; */
            }
            $totalPagar = ($totalPagar + $totalIva + $subtotal) - $retencion;
            $totalIva = 0;
            $subtotal = 0;
        }

        return number_format($totalPagar, 2, '.', ',');
    }

    public function calcularRetencionPreOrdenProveedor($proveedor)
    {
        $subtotal = 0;
        foreach ($proveedor['productos'] as $producto) {
            $subtotal = $subtotal + ((($producto['cantidad'] * $producto['precio']) * 0.16) * $producto['retencion'] / 100);
        }

        return number_format($subtotal, 2, '.', ',');
    }

    public function validarDivisa()
    {
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
                return ['error' => '', 'status' => $divisaADD];
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

    public function mount($requisicion)
    {
        $this->allDetallesSelected = [];
        $this->requiCalculos = $requisicion;

        //DIVISA 
        $horaActual = Carbon::now();
        
        //dd($divisaPeso,$divisaPesoActual);
        if ($horaActual->lt(Carbon::today()->addSeconds(43200))) { // Antes de las 12:00:00
            $divisaPeso = Divisa::whereDate('created_at', Carbon::today())
                ->where('moneda', 'USD')
                ->orderBy('created_at', 'desc')
                ->first();
        } else { // Después o igual a las 12:00:00
            $divisaPeso = Divisa::whereBetween('created_at', [
                    Carbon::today()->addSeconds(43200), // Hoy a las 12:00:00
                    Carbon::today()->endOfDay()         // Hoy a las 23:59:59
                ])
                ->where('moneda', 'USD')
                ->orderBy('created_at', 'desc')
                ->first();
        }
        //dd($divisaPeso);
        if ($divisaPeso) {
            $this->valorPeso = $divisaPeso->valor;
        } else {
            //dd('no hay moneda');
            $respValorDivisa = $this->validarDivisa();
            if ($respValorDivisa['error'] != '') {
                dd($respValorDivisa);
            } else {

                $this->valorPeso = $respValorDivisa['status']['valor'];
            }
        }

        $user = auth()->user();
        $userSolictante = User::find($requisicion->user_id);
        $permiso = permisosrequisicion::where('PuestoAutorizador_id', $user->puesto->id)
            ->where('departamento_id', $userSolictante->departamento_id)
            ->first();
        //dd($permiso);
        $this->totalPermitidoAutorizar = $permiso->monto;

        $cotizacionesRequisicion = Cotizacion::select('id')->where('requisicion_id', '=', $requisicion->id)->get();
        $allCotizaciones = [];
        foreach ($cotizacionesRequisicion as $cr) {
            $coti = DetalleCotizacion::select('id', 'cotizacion_id', 'producto_id', 'autorizado')->where('cotizacion_id', '=', $cr->id)->get();
            array_push($allCotizaciones, ["cotizacion_id" => $cr->id, "cotizaciones" => $coti]);
        }
        //dd($cotizacionesRequisicion);
        $index = 0;
        $allProcudots = [];
        foreach ($allCotizaciones as $AC) {
            foreach ($AC['cotizaciones'] as $producto) {
                array_push($allProcudots, $producto);
                if ($producto['autorizado'] == 1) {
                    array_push($this->allDetallesSelected, $producto);
                    array_push($this->indexProdAdded, $index); // En caso de recargar pagina valida los index agregados previamente
                }
                $index++;
            }
            $index = 0;
        }
        foreach ($allProcudots as $producto) {
            if ($producto->autorizado == 1) {
                if ($producto->producto_id == 4155) {
                    //dd('tiene diesel');
                    $this->contieneDiesel = true;
                }
                if ($producto->producto_id != 4155) {
                    //dd('no contiene tiene diesel');
                    $this->contieneProductoDifDiesel = true;
                }
            }
        }

        $this->urlApi = ApiUrl::urlApi();
        $this->requisicion = $requisicion;

        $this->PreDataOrden = $this->generarPrevOrdenCompra();

        $this->visto();
    }
    public function render()
    {
        $this->PreDataOrden = $this->generarPrevOrdenCompra();
        return view('livewire.requisicion.autorizar');
    }
}
