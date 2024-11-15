<div class="p-2">

    <livewire:requisicion.component.informacion-requisicion :requisicion="$requisicion" />

    <div>
        @if ($requisicion->cotizaciones->count() != 0)
            <form wire:submit.prevent="continuarAutorizar()">
                <div class="w-full">
                    @foreach ($requisicion->cotizaciones as $cotizacion)
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 mt-4">
                            <thead class="text-md font-bold uppercase bg-gray-50">
                                <tr>
                                    <th colspan="5" class="px-6 py-3 font-bold text-black">
                                        <div class="flex justify-between">
                                            <p>
                                                Proveedor: {{ $cotizacion['proveedor'] }}
                                            </p>
                                            <p>
                                                Tiempo Entrega: {{ $cotizacion['dias_entrega'] }}
                                            </p>
                                            <p>
                                                Total Cotizacion =
                                                ${{ number_format(
                                                    $cotizacion->detalleCotizaciones->sum(function ($detalle) {
                                                        return $detalle->cantidad * $detalle->precio * 1.16;
                                                    }),
                                                    2,
                                                    '.',
                                                    ',',
                                                ) }}


                                            </p>
                                            <button wire:click.prevent="download({{ $cotizacion->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                                    <path
                                                        d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                                    <path
                                                        d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                </tr>


                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Cantidad
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Producto
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Precio Cotizado
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
                                            <td class="w-4 p-4">
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
                                            <td class="px-6 py-4">
                                                <input
                                                    wire:change="updateCantidad( {{ $detalle['id'] }} , $event.target.value)"
                                                    type="number" value="{{ $detalle['cantidad'] }}"
                                                    id="{{ $detalle['id'] }}">
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $loop->index + 1 }}. {{ $detalle['producto'] }}
                                            </td>
                                            <td class="px-6 py-4">
                                                ${{ $detalle['precio'] }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="#"
                                                    wire:click="abrirModal({{ $detalle['producto_id'] }})"
                                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Ver</a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>

                <div class="my-3 d-flex justify-between">
                    {{-- <p>{{ $this->obtenerTotalAutorizar() }}</p>
                    <p>{{ $totalPermitidoAutorizar }}</p>
                    <p>{{ $totalPermitidoAutorizar > $this->obtenerTotalAutorizar() ? 'true' : 'false' }}</p> --}}
                    @if ($totalPermitidoAutorizar > $this->obtenerTotalAutorizar() || ($contieneDiesel && !$contieneProductoDifDiesel))
                        <x-button id="btnAutorizar" type="submit" wire:loading.attr="disabled">Autorizar</x-button>
                    @else
                        <x-button id="btnAutorizar" type="submit" wire:loading.attr="disabled">Autorizar Siguiente
                            Nivel</x-button>
                    @endif

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
                <textarea wire:model="comentario" class="w-full border rounded-lg p-2 mb-2" placeholder="Comentario..." rows="4"></textarea>
                <x-input-error for="comentario" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="saveComentario()">Guardar</x-button>
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
                <textarea wire:model="comentario" class="w-full border rounded-lg p-2 mb-2" placeholder="Comentario..." rows="4"></textarea>
                <x-input-error for="comentario" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="saveComentarioCancelado()">Guardar</x-button>
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
                    rows="4"></textarea>
                <x-input-error for="comentariofinal" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="saveComentarioFinal()">Guardar</x-button>

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
                    rows="1"></textarea>
                <x-input-error for="comentariofinalautorizar" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button class="btn btn-secondary btn-sm me-3"
                wire:click="$set('comentarioFinalAutorizar', false)">Cancelar</x-button>
            <x-button wire:click="saveComentarioFinalAutorizar()" wire:loading.attr="disabled">Autorizar</x-button>
        </x-slot>
    </x-alpine-modal>



    <script>
        window.addEventListener('ProductoYaAutoriado', event => {
            //console.log($(`checkbox-table-search-${event.detail[0].id}`));
            var id = event.detail[0].id

            $(`#checkbox-table-search-${id}`).prop('checked', false);
        });
        $(document).ready(function() {
            // Existing jQuery code
            let detallesPorActualizar = [];
            $(document).on('ObtenerCantidad', function(event) {


                console.log("click");

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
