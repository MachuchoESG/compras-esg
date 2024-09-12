<div class="p-4 border rounded mb-4">


    <div class="w-full md:flex md:justify-between md:items-center">


        <div class="flex flex-col">
            <span class="text-sm text-gray-500"> Fecha Creacion {{ \Carbon\Carbon::parse($requisicion->created_at)->locale('es')->isoFormat('LL') }}</span>
            <p class="text-gray-500 text-md font-bold mb-0">Folio: {{ $requisicion->folio }}</p>
            @if ($requisicion->ordenCompra)
            <p class="text-gray-500 text-md font-bold mb-0">Orden de Compra: {{ $requisicion->ordenCompra }}</p>
            @endif
            @if ($requisicion->proyecto)
            <p class="text-gray-500 text-md font-bold">Proyecto: {{ $requisicion->proyecto }}</p>
            @endif
        </div>

        @if ($requisicion->evidencia()->exists())
        <button wire:click.prevent="downloadevidencia({{ $requisicion->evidencia()->first()->id }})" class=" relative inline-flex items-center  px-1 py-2 text-sm font-medium rounded-lgfocus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 ">
            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z" />
                <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
            </svg>
            Evidencia
        </button>
        @endif
    </div>


    <div class="md:flex md:justify-between ">
        <p class="text-gray-500 text-md">Solicitante: {{ $requisicion->solicitante->getNombreCompleto() }}</p>


        <p>
            @if($this->estatus())
            <span class="bg-emerald-200 text-emerald-700 text-sm font-bold me-2 px-2.5 py-0.5 rounded">{{ $this->estatus() }}</span>
            @endif
        </p>

        <p class="text-gray-500 text-md">
            Fecha Requerida: {{ \Carbon\Carbon::parse($requisicion->fecharequerida)->locale('es')->isoFormat('LL') }}
            ({{ \Carbon\Carbon::parse($requisicion->fecharequerida)->locale('es')->diffForHumans() }})
        </p>

    </div>

    <p class="text-gray-500 text-md">Observaciones: {{ $requisicion->observaciones }}</p>

    @if(!empty($requisicion->unidad))
        <p class="text-gray-500 text-md">Unidad de Taller: {{ $requisicion->unidad }}</p>
    @endif

    @if($requisicion->comentarios->count() > 0)
    <x-button wire:click="$set('openComentarios', true)" class="my-2">Ver Comentarios</x-button>
    @endif

    <livewire:requisicion.component.comentarios :requisicion="$requisicion" wire:model="openComentarios" />


    <div class="w-full text-sm relative  overflow-x-auto ">
        <table class=" w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-center ">
                    <th class="md:py-2 md:px-4 border-b ">Cantidad</th>
                    <th class="md:py-2 md:px-4 border-b ">Producto</th>
                    <th class="md:py-2 md:px-4 border-b ">Observaciones</th>

                    <!-- Agrega más columnas según tus necesidades -->
                </tr>
            </thead>
            <tbody>

                @foreach ($requisicion->detalleRequisiciones as $detalle)
                <tr class="text-center @if($detalle->producto_id == 0) bg-red-200 @endif">
                    <td class="md:py-2 md:px-4 border-b">{{ $detalle->cantidad }}</td>
                    <td class="md:py-2 md:px-4 border-b">{{ $detalle->producto }}</td>
                    <td class="md:py-2 md:px-4 border-b">{{ $detalle->observaciones }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>



    <section class="mt-2">

        <p class="text-gray-500 text-md font-bold">
            {{ $cotizaciones->count() }}
            {{ $cotizaciones->count() == 1 ? 'Cotización' : 'Cotizaciones' }}
        </p>


        @if ($cotizaciones->isNotEmpty())
        <div class="w-full overflow-x-auto">
            @foreach ($cotizaciones as $cotizacion)
            <div x-data="{ openTab: false }" class="border rounded-md mb-4">
                <div x-on:click="openTab = !openTab" class="border-b">
                    <div class="flex justify-between items-center px-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button py-2 px-4 w-full text-left" type="button">
                                Proveedor: {{ $cotizacion->proveedor }}
                            </button>

                        </h2>
                        <span class="text-sm text-gray-500"> Fecha documento {{ \Carbon\Carbon::parse($cotizacion->created_at)->locale('es')->isoFormat('LL') }}</span>
                    </div>

                    <div x-show="openTab" class="bg-gray-100 py-2 px-4">
                        <div class="flex justify-around  flex-wrap text-sm mb-2">
                            <p class="mb-1">Tiempo Entrega: {{ $cotizacion->dias_entrega }}</p>
                            <p class="mb-1">Días Crédito: {{ $cotizacion->dias_credito }}</p>
                            <p class="mb-1">Forma Pago: {{ $cotizacion->formapago }}</p>
                            <button wire:click.prevent="download({{ $cotizacion->id }})" class="flex items-start">
                                <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z" />
                                    <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                </svg>
                                <span>Cotización</span>
                            </button>
                        </div>

                        <table class="w-full text-sm text-gray-500 mt-2">
                            <thead class="bg-gray-50">
                                <tr class="text-center">
                                    <th scope="col" class="px-6 py-3">Cantidad</th>
                                    <th scope="col" class="px-6 py-3">Producto</th>
                                    <th scope="col" class="px-6 py-3">Precio Cotizado</th>
                                    <th scope="col" class="px-6 py-3">Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cotizacion->detalleCotizaciones as $detalle)
                                <tr class="bg-white text-center border-b">
                                    <td class="px-6 py-3">{{ $detalle->cantidad }}</td>
                                    <td class="px-6 py-3">{{ $detalle->producto }}</td>
                                    <td class="px-6 py-3">${{ $detalle->precio }}</td>
                                    <td class="px-6 py-3">
                                        @if($detalle->autorizado)
                                        <span class=" bg-emerald-200  text-emerald-700 text-sm font-bold me-2 px-2.5 py-0.5 rounded">Autorizado</span>
                                        @else

                                        @if (in_array($requisicion->estatus_id, [1, 2, 3, 7]))
                                        <span class="bg-emerald-200 text-emerald-700 text-sm font-bold me-2 px-2.5 py-0.5 rounded">Pendiente</span>
                                        @else
                                        <span class="bg-red-500 text-white text-sm font-bold me-2 px-2.5 py-0.5 rounded">No autorizado</span>
                                        @endif



                                        @endif
                                    </td>




                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-600 text-center">Aún no cuenta con cotizaciones.</p>
        @endif

    </section>


    <section class="text-end">

        @if($requisicion->estatus->name == 'Finalizada'  || $requisicion->estatus->name == 'Cotizacion cargada' || $requisicion->estatus->name == 'No autorizada')
        @foreach($autorizacionHistorial as $historial)
            @if($historial->autorizado)
            <p class="text-gray-500 text-md">Autorizado por {{ $historial->autorizador->name }} el {{ \Carbon\Carbon::parse($historial->updated_at)->locale('es')->isoFormat('LLLL') }}</p>

            @else
                @if($requisicion->estatus->name == 'Finalizada')
                    <p class="text-gray-500 text-md">Autorizado por  {{ $historial->autorizador->name }} el {{ \Carbon\Carbon::parse($historial->updated_at)->locale('es')->isoFormat('LLLL') }}</p>
                @endif
                @if($requisicion->estatus->name == 'No autorizada')
                    <p class="text-gray-500 text-md">No autorizado por {{ $historial->autorizador->name }} el {{ \Carbon\Carbon::parse($historial->updated_at)->locale('es')->isoFormat('LLLL') }}</p>
                @endif
            @endif
        @endforeach
        @endif



    </section>



</div>