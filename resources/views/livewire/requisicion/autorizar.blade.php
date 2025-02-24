<div class="p-2">

    <livewire:requisicion.component.informacion-requisicion :requisicion="$requisicion" />

    <div>
        <div class="card p-3 table-responsive mb-3">
            {{-- <button wire:click="showDD">ver indexes</button> --}}
            <h4>Orden de Compra (MXN)</h4>
            <table class="w-full text-sm text-gray-500 mb-3" style="min-width: 800px">
                <thead>
                    <tr>
                        <th>Proveedor</th>
                        <th class="text-end">SUBTOTAL</th>
                        <th class="text-end">IVA</th>
                        <th class="text-end">RETENCION</th>
                        <th class="text-end">TOTAL</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    {{-- {{ $this->generarPrevOrdenCompra() }} --}}
                    @foreach ($this->PreDataOrden as $pre)
                        <tr>
                            <td>{{ $pre['proveedor'] }}</td>
                            <td class="text-end"> ${{ $this->calcularSubtotalPreOrdenProveedor($pre) }} </td>
                            <td class="text-end"> ${{ $this->calcularIVAPreOrdenProveedor($pre) }}</td>
                            <td class="text-end"> ${{ $this->calcularRetencionPreOrdenProveedor($pre) }}</td>
                            <td class="text-end"> ${{ $this->calcularTotalPreOrdenProveedor($pre) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="12">
                            <p></p>
                        </td>
                        {{-- <td class="text-end fw-bold">Maximo Autorizar: ${{ number_format($totalPermitidoAutorizar, 2, '.', ',') }}</td> --}}
                    </tr>
                    <tr class="bg-black text-white">
                        {{-- <td colspan="" class="text-start fw-bold"></td> --}}
                        <td colspan="12" class="text-end fw-bold">
                            <p class="m-0">Total Pagar:
                                ${{ $this->calcularTotalPagarPreOrdenProveedor($this->PreDataOrden) }}</p>
                            <p class="m-0">Maximo Autorizar:
                                ${{ number_format($totalPermitidoAutorizar, 2, '.', ',') }}</p>

                        </td>
                    </tr>
                </tbody>
            </table>
            {{-- <ul class="list-group">
                @foreach ($this->PreDataOrden as $pre)
                    <li class="list-group-item">{{ json_encode($pre) }}</li>
                @endforeach
            </ul> --}}
        </div>
        @if ($requisicion->cotizaciones->count() != 0)
            <form wire:submit.prevent="continuarAutorizar()">
                <div class="w-full card p-2">
                    @foreach ($requisicion->cotizaciones as $cotizacion)
                        <div class="row bg-black text-white mx-0 rounded-3">
                            <div class="col-3 text-center my-2">
                                <p class="m-0">
                                    Proveedor: {{ $cotizacion['proveedor'] }}
                                </p>
                            </div>
                            <div class="col-3 text-center my-2">
                                <p class="m-0">
                                    Tiempo Entrega: {{ $cotizacion['dias_entrega'] }}
                                </p>
                            </div>
                            <div class="col-2 text-center my-2">
                                Tipo Moneda: {{ $cotizacion['moneda'] }}
                            </div>
                            <div class="col-3 text-center my-2">
                                @if ($cotizacion['moneda'] == 'USD')
                                    Valor Divisa: {{ number_format($this->valorPeso, 2, '.', ',') }}
                                @endif
                            </div>
                            <div class="col-1 d-flex justify-content-end algin-items-sm-start algin-items-center">
                                <button wire:click.prevent="download({{ $cotizacion->id }})" class="btn text-white"
                                    style="height: 50%">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                        <path
                                            d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                        <path
                                            d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                                    </svg>
                                </button>
                            </div>
                            {{-- <div class="col text-center">
                                <p>Subtotal:
                                    ${{ number_format($this->generarCalculoSubtotal($cotizacion->id), 2, '.', ',') }}
                                </p>
                            </div>
                            <div class="col text-center">
                                <p>IVA:
                                    ${{ number_format($this->generarCalculoIVA($cotizacion->id), 2, '.', ',') }}
                                </p>
                            </div>
                            <div class="col text-center">
                                <p>Retención:
                                    ${{ number_format($this->generarCalculoRetencion($cotizacion->id), 2, '.', ',') }}
                                </p>
                            </div>
                            <div class="col text-center">
                                <p>
                                    Total =
                                    ${{ number_format(
                                        $this->generarCalculoSubtotal($cotizacion->id) +
                                            $this->generarCalculoIVA($cotizacion->id) -
                                            $this->generarCalculoRetencion($cotizacion->id),
                                        2,
                                        '.',
                                        ',',
                                    ) }}
                                </p>
                            </div> --}}
                            {{-- <div class="col text-center">
                                <p class="">
                                    Maximo a Autorizar =
                                    ${{ number_format($totalPermitidoAutorizar, 2, '.', ',') }}
                                </p>
                            </div> --}}

                        </div>
                        <div class="table-responsive">
                            <table class="w-full text-sm text-gray-500 mb-3" style="min-width: 800px">
                                <thead class="text-md font-bold uppercase">
                                    <tr class="text-center">
                                        <th scope="col" class="px-6 py-3">
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Cantidad
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Producto
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Precio Unitario
                                        </th>
                                        {{-- @if ($cotizacion['moneda'] === 'USD') 
                                        <th scope="col" class="px-6 py-3">
                                            Precio Divisa
                                        </th>
                                        @endif --}}
                                        <th scope="col" class="px-6 py-3">
                                            SubTotal
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            IVA
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Retención
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Total
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Historial de Compra
                                        </th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @foreach ($cotizacion->detalleCotizaciones as $detalle)
                                        @if ($detalle['precio'] != 0)
                                            <tr class="bg-white border-b">
                                                <td class="w-4 p-2">
                                                    <div class="flex items-center">
                                                        <input id="checkbox-table-search-{{ $detalle['id'] }}"
                                                            wire:click="toggleSelection( {{ $loop->index }} ,{{ $detalle->id }} ,$event.target.checked  )"
                                                            type="checkbox"
                                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                            @if ($detalle['autorizado']) checked @endif>
                                                        <label for="checkbox-table-search-{{ $detalle['id'] }}"
                                                            class="sr-only">checkbox</label>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-2">
                                                    <input style="width: 80px"
                                                        wire:change="updateCantidad( {{ $detalle['id'] }} , $event.target.value)"
                                                        type="number" value="{{ $detalle['cantidad'] }}"
                                                        class="form-control-sm" id="{{ $detalle['id'] }}">
                                                </td>
                                                <td class="px-6 py-2">
                                                    {{ $loop->index + 1 }}. {{ $detalle['producto'] }}
                                                </td>
                                                <td class="px-6 py-2 text-center">
                                                    ${{ number_format($detalle['precio'], 2, '.', ',') }}
                                                </td>
                                                {{-- @if ($cotizacion->moneda == 'USD') 
                                                <td class="px-6 py-2 text-center">
                                                    ${{ number_format(($detalle['precio'] * $this->valorPeso), 2, '.', ',') }}
                                                </td>
                                                @endif --}}
                                                <td class="px-6 py-2 text-center">
                                                    {{-- @if ($cotizacion->moneda == 'USD')
                                                    ${{ number_format($this->generarCalculoSubtotalDetalle($detalle, $cotizacion->moneda), 2, '.', ',') }} --}}
                                                    {{-- @else --}}
                                                    ${{ number_format($this->generarCalculoSubtotalDetalle($detalle), 2, '.', ',') }}
                                                    {{-- @endif --}}

                                                </td>
                                                <td class="text-center">
                                                    {{-- @if ($cotizacion->moneda == 'USD')
                                                    ${{ number_format($this->generarCalculoIVADetalle($detalle, $cotizacion->moneda), 2, '.', ',') }}
                                                    @else --}}
                                                    ${{ number_format($this->generarCalculoIVADetalle($detalle), 2, '.', ',') }}
                                                    {{-- @endif --}}

                                                </td>
                                                <td class="text-center">
                                                    {{-- @if ($cotizacion->moneda == 'USD')
                                                    ${{ number_format($this->generarCalculoRetencionDetalle($detalle, $cotizacion->moneda), 2, '.', ',') }}
                                                    @else --}}
                                                    ${{ number_format($this->generarCalculoRetencionDetalle($detalle), 2, '.', ',') }}
                                                    {{-- @endif --}}

                                                </td>

                                                <td class="px-6 py-2 text-center">
                                                    {{--  @if ($cotizacion->moneda == 'USD')
                                                    ${{ number_format($this->generarCalculoTotalDetalle($detalle, $cotizacion->moneda), 2, '.', ',') }}
                                                    @else --}}
                                                    ${{ number_format($this->generarCalculoTotalDetalle($detalle), 2, '.', ',') }}
                                                    {{-- @endif --}}

                                                </td>
                                                <td class="px-6 py-2 text-center">
                                                    <a href="#"
                                                        wire:click="abrirModal({{ $detalle['producto_id'] }})"
                                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Ver</a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <br>
                    @endforeach
                </div>

                {{-- <div>
                    <p>total autorizar - {{ $this->obtenerTotalAutorizar() }}</p>
                    <p>Permitido - {{ $totalPermitidoAutorizar }}</p>
                    <p>{{ $totalPermitidoAutorizar > $this->obtenerTotalAutorizar() ? 'true' : 'false' }}</p>
                </div> --}}

                <div class="my-3 d-flex justify-between">
                    @php
                        $puedeAutorizar = $totalPermitidoAutorizar > $this->obtenerTotalAutorizar();
                        $esSoloDiesel = $contieneDiesel && !$contieneProductoDifDiesel;
                    @endphp
                    {{-- @if ($totalPermitidoAutorizar > $this->obtenerTotalAutorizar())
                        <p>autorizado monto - {{ $totalPermitidoAutorizar > $this->obtenerTotalAutorizar() }}</p>
                        <div>
                            <p>monto: {{$this->obtenerTotalAutorizar()}}</p>
                            <p>maximo: {{$totalPermitidoAutorizar}}</p>
                        </div>
                    @endif
                    
                    @if ($esSoloDiesel)
                        <p>contiene diesel</p>
                    @endif

                    @if ($contieneProductoDifDiesel)
                        <p>contienededif diesel</p>
                    @endif --}}

                    @if ($puedeAutorizar || $esSoloDiesel)
                        <x-button id="btnAutorizar" type="submit" wire:loading.attr="disabled">Autorizar</x-button>
                    @else
                        <x-button id="btnAutorizar" type="submit" wire:loading.attr="disabled">Autorizar Siguiente
                            Nivel</x-button>
                    @endif

                    {{-- @if ($totalPermitidoAutorizar > $this->obtenerTotalAutorizar() || ($contieneDiesel && !$contieneProductoDifDiesel))
                        <x-button id="btnAutorizar" type="submit" wire:loading.attr="disabled">Autorizar</x-button>
                    @else
                        <x-button id="btnAutorizar" type="submit" wire:loading.attr="disabled">Autorizar Siguiente
                            Nivel</x-button>
                    @endif --}}

                    <x-danger-button wire:click.prevent="noAutorizar">No autorizar</x-danger-button>
                    <x-button wire:click.prevent="volverCotizar">Volver a cotizar</x-button>
                </div>



            </form>
        @endif
    </div>




    <x-dialog-modal wire:model='open'>
        <x-slot name='title'>
            Historial compra
        </x-slot>
        <x-slot name='content'>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                @if (!empty($historialCommpra) && is_array($historialCommpra) && count($historialCommpra) > 0)
                    @foreach ($historialCommpra as $cotizacion)
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 mt-4">
                            <thead class="text-md font-bold uppercase bg-gray-50">
                                <tr>
                                    <th colspan="6" class="px-6 py-3 font-bold text-black">
                                        {{ $cotizacion['proveedor']['crazonsocial'] }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Precio
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Fecha Ultima Compra
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-white border-b">
                                    <td class="px-6 py-4">
                                        ${{ $cotizacion['cpreciocompra'] }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::createFromFormat('m/d/Y H:i:s:v', $cotizacion['ctimestamp'])->format('m/d/Y') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        Aún no cuenta con cotizaciones.
                    </div>
                @endif
            </div>

        </x-slot>

        <x-slot name='footer'>
            <x-button wire:click="$set('open', false)">Salir</x-button>
        </x-slot>
    </x-dialog-modal>

    <x-alpine-modal wire:model="comentarioOpen">
        <x-slot name="title">
            Solicitud de Autorización
        </x-slot>
        <x-slot name="content">
            <div>
                <p class="text-gray-500 text-md font-bold">Solicitar autorización a {{ $jefe }}</p>
            </div>
            <div class="w-full">
                <x-label>Comentario</x-label>
                <textarea wire:model="comentario" class="w-full border rounded-lg p-2 mb-2" placeholder="Comentario..."
                    rows="4" id="input_comentario_solicitud_autorizar"></textarea>
                <x-input-error for="comentario" />
                <p style="font-size: .8rem">Caracteres <span id="contador_solicitud_autorizacion">0</span>/255</p>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="saveComentario()" id="btn_comentario_solicitud_autorizar">Guardar</x-button>
            <x-button wire:click="$set('comentarioOpen', false)">Cancelar</x-button>
        </x-slot>
    </x-alpine-modal>

    <x-alpine-modal wire:model="openCancelacion">
        <x-slot name="title">
            Motivo de Cancelacion
        </x-slot>
        <x-slot name="content">

            <div class="w-full">
                <x-label>Comentario</x-label>
                <textarea wire:model="comentario" class="w-full border rounded-lg p-2 mb-2" placeholder="Comentario..."
                    rows="4" id="input_comentario_cancelacion"></textarea>
                <x-input-error for="comentario" />
                <p style="font-size: .8rem">Caracteres <span id="contador_cancelacion">0</span>/255</p>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="saveComentarioCancelado()" id="btn_comentario_cancelacion">Guardar</x-button>
            <x-button wire:click="$set('openCancelacion', false)">Cancelar</x-button>
        </x-slot>
    </x-alpine-modal>

    <x-alpine-modal wire:model="comentarioFinal">
        <x-slot name="title">
            Comentario de Autorizacion
        </x-slot>
        <x-slot name="content">

            <div class="w-full">
                <x-label>Comentario</x-label>
                <textarea wire:model="comentariofinal" class="w-full border rounded-lg p-2 mb-2" placeholder="Comentario..."
                    rows="4" id="input_comentario_autorizacion"></textarea>
                <x-input-error for="comentariofinal" />
                <p style="font-size: .8rem">Caracteres <span id="contador_autorizacion">0</span>/255</p>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="saveComentarioFinal()" id="btn_comentario_autorizacion">Guardar</x-button>

        </x-slot>
    </x-alpine-modal>

    <x-alpine-modal wire:model="comentarioFinalAutorizar">
        <x-slot name="title">
            Comentario Final Antes de Autorizar
        </x-slot>
        <x-slot name="content">

            <div class="w-full">
                <p class="fw-bold">El comentario final antes de autorizar no es obligatorio.</p>
                <x-label>Comentario</x-label>
                <textarea wire:model="comentariofinalautorizar" class="w-full border rounded-lg p-2 mb-2" placeholder="Comentario..."
                    id="input_comentario_final_autorizar" rows="3"></textarea>
                <x-input-error for="comentariofinalautorizar" />
                <p style="font-size: .8rem">Caracteres <span id="contador_final_autorizar">0</span>/255</p>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button class="btn btn-secondary btn-sm me-3"
                wire:click="$set('comentarioFinalAutorizar', false)">Cancelar</x-button>
            <x-button id="btn_comentario_final_autorizar" wire:click="saveComentarioFinalAutorizar()"
                wire:loading.attr="disabled">Autorizar</x-button>
        </x-slot>
    </x-alpine-modal>


    <script>
        window.addEventListener('ProductoYaAutoriado', event => {
            var id = event.detail[0].id
            $(`#checkbox-table-search-${id}`).prop('checked', false);
        });

        $('#input_comentario_solicitud_autorizar').on('input', function(e) {
            let comentSoliCancelacion = e.target.value;
            $('#contador_solicitud_autorizacion').text(comentSoliCancelacion.length);
            if (comentSoliCancelacion === '' || comentSoliCancelacion.length > 255) {
                //console.log('Comentario obligarotiros');
                $('#btn_comentario_solicitud_autorizar').prop('disabled', true);
                $('#btn_comentario_solicitud_autorizar').hide();
                //alert('Comentario para finalizar requisicion con Cotizacion Unica es obligatorio.')
            } else {
                //console.log('coment valido');
                $('#btn_comentario_solicitud_autorizar').prop('disabled', false);
                $('#btn_comentario_solicitud_autorizar').show();
            }

        })

        $('#input_comentario_cancelacion').on('input', function(e) {
            let comentCancelacion = e.target.value;
            $('#contador_cancelacion').text(comentCancelacion.length);
            if (comentCancelacion === '' || comentCancelacion.length > 255) {
                //console.log('Comentario obligarotiros');
                $('#btn_comentario_cancelacion').prop('disabled', true);
                $('#btn_comentario_cancelacion').hide();
                //alert('Comentario para finalizar requisicion con Cotizacion Unica es obligatorio.')
            } else {
                //console.log('coment valido');
                $('#btn_comentario_cancelacion').prop('disabled', false);
                $('#btn_comentario_cancelacion').show();
            }

        })

        $('#input_comentario_final_autorizar').on('input', function(e) {
            let comentAutorizacion = e.target.value;
            $('#contador_autorizacion').text(comentAutorizacion.length);
            if (comentAutorizacion === '' || comentAutorizacion.length > 255) {
                //console.log('Comentario obligarotiros');
                $('#btn_comentario_autorizacion').prop('disabled', true);
                $('#btn_comentario_autorizacion').hide();
                //alert('Comentario para finalizar requisicion con Cotizacion Unica es obligatorio.')
            } else {
                //console.log('coment valido');
                $('#btn_comentario_autorizacion').prop('disabled', false);
                $('#btn_comentario_autorizacion').show();
            }

        })

        $('#input_comentario_autorizacion').on('input', function(e) {
            let comentFinal = e.target.value;
            $('#contador_final_autorizar').text(comentFinal.length);
            if (comentFinal === '' || comentFinal.length > 255) {
                //console.log('Comentario obligarotiros');
                $('#btn_comentario_final_autorizar').prop('disabled', true);
                $('#btn_comentario_final_autorizar').hide();
                //alert('Comentario para finalizar requisicion con Cotizacion Unica es obligatorio.')
            } else {
                //console.log('coment valido');
                $('#btn_comentario_final_autorizar').prop('disabled', false);
                $('#btn_comentario_final_autorizar').show();
            }

        })

        $(document).ready(function() {
            // Existing jQuery code
            let detallesPorActualizar = [];
            $(document).on('ObtenerCantidad', function(event) {

                const detalles = event.detail.lista;

                detalles.forEach(element => {
                    const id = element.id;
                    const input = document.getElementById(id);
                    const cantidad = input.value;

                    // Agrega los detalles al array
                    detallesPorActualizar.push({
                        id: id,
                        cantidad: parseInt(cantidad)
                    });
                });

                console.log(detallesPorActualizar);

                Livewire.dispatch('Actualizar', {
                    lista: detallesPorActualizar
                });

                detallesPorActualizar = [];
            });
        });
    </script>
</div>
