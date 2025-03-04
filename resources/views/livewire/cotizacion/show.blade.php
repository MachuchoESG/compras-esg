<div class="p-2">

    <livewire:requisicion.component.informacion-requisicion :requisicion="$requisicion" />




    <div class="flex justify-end my-4 mr-2">
        @if ($requisicion->estatus_id !== 6)
            @if (!$contieneProductoSinRegistrar)
                @if ($requisicion->cotizaciones->isNotEmpty())
                    <div class="me-5">
                        <div class="form-check pt-1">
                            <input class="form-check-input" type="checkbox" id="cotizacion_unica"
                                wire:click="toggleCotizacionUnica($event.target.checked)" @checked($requisicion->cotizacion_unica)>
                            <label class="fw-bold" class="form-check-label" for="flexCheckDefault">
                                Cotización Unica
                            </label>
                        </div>

                    </div>
                @endif
                {{-- <button @disabled($esCotizacionUnica) id="btnModalAddCotizacion" type="button" class="btn btn-primary"
                    data-bs-toggle="modal" data-bs-target="#ModalAddCotizacion">
                    Agregar Cotizacion
                </button> --}}

                <button @disabled($esCotizacionUnica) id="btnModalAddCotizacion" type="button" class="btn btn-primary"
                    data-bs-toggle="modal" data-bs-target="#modalAddCotizacion">
                    Agregar Cotizacion
                </button>
            @else
                <p class="text-danger font-bold">Antes de cotizar se debe Registrar y Asignar los productos faltantes en
                    el
                    sistema.</p>
            @endif
        @endif
    </div>

    @if ($requisicion->detalleRequisiciones->isNotEmpty())
        <div class="w-full text-sm relative ">
            <table class=" w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-200 text-center ">
                        <th class="md:py-2 md:px-4 border-b ">Cantidad</th>
                        <th class="md:py-2 md:px-4 border-b ">Producto</th>
                        <th class="md:py-2 md:px-4 border-b ">Observaciones</th>
                        <th class="md:py-2 md:px-4 border-b  absolute">Accion</th>
                        <!-- Agrega más columnas según tus necesidades -->
                    </tr>
                </thead>
                <tbody>



                    @foreach ($requisicion->detalleRequisiciones as $detalle)
                        <tr class="text-center @if ($detalle->producto_id == 0) bg-red-200 @endif">
                            <td class="md:py-2 md:px-4 border-b">{{ $detalle->cantidad }}</td>
                            <td class="md:py-2 md:px-4 border-b">{{ $detalle->producto }}</td>
                            <td class="md:py-2 md:px-4 border-b">{{ $detalle->observaciones }}</td>
                            <td class="md:py-2 md:px-4 border-b ">

                                <div class="">
                                    <div class=" text-justify relative">
                                        <x-dropdown align="right" width="60">
                                            <x-slot name="trigger">
                                                <span class="inline-flex rounded-md">
                                                    <button
                                                        class="px-1 py-1 text-black transition-colors duration-200 rounded-lg dark:text-gray-300 hover:bg-gray-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                                                        </svg>
                                                    </button>
                                                </span>
                                            </x-slot>

                                            <x-slot name="content">
                                                <div class="w-60">
                                                    <div class="block px-4 py-2 text-xs bg-gray-400 text-gray-100">
                                                        {{ __('Acciones') }}
                                                    </div>

                                                    <x-dropdown-link id="alta-producto"
                                                        class="no-underline text-xs cursor-pointer">
                                                        <div>
                                                            <button wire:click="AbrirModalAltaProducto()">Alta
                                                                Producto</button>
                                                        </div>
                                                    </x-dropdown-link>
                                                    <x-dropdown-link id="editar-producto"
                                                        class="no-underline text-xs cursor-pointer">
                                                        <div>
                                                            <button data-bs-toggle="modal"
                                                                data-bs-target="#ModalEditProducto"
                                                                onclick="getListaProductosSucursal({{ $detalle->id }})">Asignar
                                                                Producto</button>
                                                        </div>
                                                    </x-dropdown-link>

                                                </div>
                                            </x-slot>
                                        </x-dropdown>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-600">No hay detalles disponibles para esta requisición.</p>
    @endif




    @if ($requisicion->cotizaciones->isNotEmpty())
        <div class="mt-3 px-3 w-100">
            @foreach ($requisicion->cotizaciones as $cotizacion)
                <div class="row bg-gray-200 px-1">
                    <div class="col-8 col-md-6 d-flex justify-start align-items-center">
                        <p class="m-0 text-start text-proveedor"> Proveedor:
                            <span>{{ $cotizacion['proveedor'] }}</span>
                        </p>

                    </div>
                    <div class="col-4 col-md-6 d-flex justify-end align-items-center pt-1">
                        <button wire:click.prevent="download({{ $cotizacion->id }})"
                            class="btn btn-secondary btn-sm d-flex items-start mx-1">
                            <x-eva-download-outline class="h-5 w-5" />
                            <span class="ms-2 fw-bold hide-text-btn">Cotización</span>
                        </button>
                        @if ($requisicion->estatus_id !== 6)
                            <button wire:click="deleteCotizacion({{ $cotizacion->id }})" wire:loading.attr="disabled"
                                class="btn btn-danger btn-sm d-flex mx-1">
                                {{-- <i class="bi bi-file-earmark-arrow-down-fill"></i> --}}
                                <x-eva-trash class="h-5 w-5" />
                                <span class="ms-2 fw-bold hide-text-btn">Eliminar</span>
                            </button>
                        @endif
                    </div>

                    <div class="col-12 mt-2">
                        <div class="row">
                            <div class="col-3">
                                <p class="text-center">
                                    Tiempo Entrega: {{ $cotizacion['dias_entrega'] }}
                                </p>
                            </div>
                            <div class="col-3">
                                <p class="text-center">
                                    Dias Credito: {{ $cotizacion['dias_credito'] }}
                                </p>
                            </div>
                            <div class="col-3">
                                <p class="text-center">
                                    Forma Pago: {{ $cotizacion['formapago'] }}
                                </p>
                            </div>
                            <div class="col-3">
                                <p class="text-center m-0">
                                    Moneda: {{ $cotizacion['moneda'] === '' ? 'MXN' : $cotizacion['moneda'] }}
                                </p>
                                @if ($cotizacion['moneda'] === 'USD')
                                    <p class="text-center m-0">
                                        Valor Peso: {{ number_format($valorPeso, 2, '.', ',') }}
                                    </p>
                                @endif

                            </div>
                        </div>

                    </div>

                </div>
                <div style="table-responsive" style="">
                    <table class="table text-sm text-gray-500 mb-3" style="overflow-x: auto">
                        <thead class=" bg-gray-50">
                            <tr class="text-center">
                                <th scope="col" class="md:px-6 md:py-3">
                                    Producto
                                </th>
                                <th scope="col" class="md:px-6 md:py-3">
                                    Cantidad
                                </th>
                                <th scope="col" class="md:px-6 md:py-3">
                                    Precio Unitario @if ($cotizacion['moneda'] === 'USD')
                                        <span>(USD)</span>
                                    @endif
                                </th>
                                {{-- @if ($cotizacion['moneda'] === 'USD')
                                    <th scope="col" class="md:px-6 md:py-3">
                                        Precio MXN
                                    </th>
                                @endif --}}
                                <th scope="col" class="md:px-6 md:py-3">
                                    Subtotal
                                </th>
                                <th scope="col" class="md:px-6 md:py-3">
                                    IVA
                                </th>
                                <th scope="col" class="md:px-6 md:py-3">
                                    Retención
                                </th>
                                <th scope="col" class="md:px-6 md:py-3">
                                    Total
                                </th>
                                <th scope="col" class="md:px-6 md:py-3">
                                    Accion
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($cotizacion->detalleCotizaciones as $detalle)
                                <tr class="bg-white border-b">
                                    <td class="md:px-6 md:py-3 col-2 col-md-3">
                                        {{ $detalle['producto'] }}
                                    </td>
                                    <td class="md:px-6 md:py-3 text-center">
                                        {{ $detalle['cantidad'] }}
                                    </td>
                                    <td class="md:px-6 md:py-3 text-center">
                                        ${{ number_format($detalle['precio'], 2, '.', ',') }}
                                    </td>
                                    {{-- @if ($cotizacion['moneda'] === 'USD')
                                        <td class="md:px-6 md:py-3 text-center">
                                            ${{ number_format($detalle['precio'] * $this->valorPeso, 2, '.', ',') }}
                                        </td>
                                    @endif --}}
                                    <td class="md:px-6 md:py-3 text-center">
                                        {{-- @if ($cotizacion['moneda'] === 'USD')
                                            ${{ number_format($this->generarCalculoSubtotalDivisa($detalle), 2, '.', ',') }}
                                        @else --}}
                                        ${{ number_format($this->generarCalculoSubtotal($detalle), 2, '.', ',') }}
                                        {{-- @endif --}}

                                    </td>
                                    <td class="text-center">
                                        {{-- @if ($cotizacion['moneda'] === 'USD')
                                            ${{ number_format($this->generarCalculoIVADivisa($detalle), 2, '.', ',') }}
                                        @else --}}
                                        ${{ number_format($this->generarCalculoIVA($detalle), 2, '.', ',') }}
                                        {{-- @endif --}}

                                    </td>
                                    <td class="text-center">
                                        {{--  @if ($cotizacion['moneda'] === 'USD')
                                            ${{ number_format($this->generarCalculoRetencionDivisa($detalle), 2, '.', ',') }}
                                        @else --}}
                                        ${{ number_format($this->generarCalculoRetencion($detalle), 2, '.', ',') }}
                                        {{-- @endif --}}

                                    </td>

                                    <td class="md:px-6 md:py-3 text-center">
                                        {{-- @if ($cotizacion['moneda'] === 'USD')
                                            ${{ number_format($this->generarCalculoTotalDivisas($detalle), 2, '.', ',') }}
                                        @else --}}
                                        ${{ number_format($this->generarCalculoTotal($detalle), 2, '.', ',') }}
                                        {{-- @endif
 --}}
                                    </td>
                                    <td>
                                        <div class="flex justify-around items-center">
                                            @if ($requisicion->estatus_id !== 6)
                                                <button wire:click="editarDetalle({{ $detalle['id'] }})"
                                                    class="text-blue-500">
                                                    <x-far-edit class="w-6 h-6" />
                                                </button>
                                            @endif


                                        </div>
                                    </td>
                                </tr>
                                @if (count($cotizacion->detalleCotizaciones) === $loop->index + 1)
                                    <tr class="">
                                        {{--  @if ($cotizacion['moneda'] === 'USD')
                                            <td colspan="7" class="fw-bold md:px-6 md:py-3"
                                                style="background-color: black; color: white;">TOTAL COTIZACION
                                            </td>
                                        @else --}}
                                        <td colspan="6" class="fw-bold md:px-6 md:py-3"
                                            style="background-color: black; color: white;">TOTAL COTIZACION
                                        </td>
                                        {{-- @endif --}}

                                        <td class="fw-bold md:px-6 md:py-3 text-center"
                                            style="background-color: black; color: white;">
                                            {{--  @if ($cotizacion['moneda'] === 'USD')
                                                ${{ number_format($this->generarTotalCotizacionDivisa($cotizacion->detalleCotizaciones), 2, '.', ',') }}
                                            @else --}}
                                            ${{ number_format($this->generarTotalCotizacion($cotizacion->detalleCotizaciones), 2, '.', ',') }}
                                            {{-- @endif --}}

                                        </td>
                                    </tr>
                                @endif
                            @endforeach


                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-600 text-center my-4">Aun no cuenta con cotizaciones.</p>
    @endif


    {{-- <div wire:ignore.self class="modal fade" id="ModalAddCotizacion" aria-hidden="true"
        aria-labelledby="exampleModalToggleLabel" tabindex="-10">
        <div class="modal-dialog modal-dialog-centered modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detalle Cotizacion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="proveedorSelect">Proveedor</label>
                    <div class="w-full">
                        <select wire:ignore class="select2 w-full" name="cotizacion.proveedor_id"
                            id="selectProveedor" wire:model.live="cotizacion.proveedor_id">
                            <option value="">Selecciona un proveedor</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor['cidclienteproveedor'] }}">
                                    {{ $proveedor['crazonsocial'] }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="cotizacion.proveedor_id" />
                    </div>

                    <div class="w-full file-input-container" x-data="{ uploading: false, progress: 0, fileUploaded: false }"
                        x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false"
                        x-on:livewire-upload-error="uploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress">

                        <label for="cotizacion">Cotizacion</label>
                        <input wire:model.live="cotizacion.image" class="w-full" type="file" id="cotizacion">
                        <!-- Progress Bar -->
                        <div class="w-full" x-show="uploading">
                            <progress class="w-full bg-red-200" max="100"
                                x-bind:value="progress"></progress>
                        </div>
                        <x-input-error for="cotizacion.image" />

                    </div>

                    <div style="overflow-y: auto; max-height: 30vh">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Precio</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requisicion->detalleRequisiciones as $detalle)
                                    <tr>

                                        <td class="col-2">{{ $detalle->cantidad }}</td>
                                        <td>{{ $detalle->producto }}</td>
                                        <td class="col-3">
                                            <input wire:model="cotizacion.precios.{{ $detalle->id }}" value="1"
                                                type="number" class="form-control col-3">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between space-x-1">
                        <div>
                            <x-label for="cantidad">Tiempo de Entrega</x-label>
                            <input wire:model="cotizacion.dias_entrega" class="w-full h-10 border rounded-lg mb-2"
                                type="number" id="cantidad" name="cantidad">
                            <x-input-error for="cotizacion.dias_entrega" />
                        </div>
                        <div>
                            <x-label for="cantidad">Dias de Credito</x-label>
                            <input wire:model="cotizacion.dias_credito" class="w-full h-10 border rounded-lg mb-2"
                                type="number" id="diascredito" name="diascredito">
                            <x-input-error for="cotizacion.dias_credito" />
                        </div>
                        <div>
                            <x-label for="formaPago">Forma de Pago</x-label>
                            <select name="cotizacion.formapago" id="cotizacion.formapago"
                                wire:model.live="cotizacion.formapago" class=" border-[#dee2e6] rounded-lg"
                                name="formaPago" id="formaPago">
                                <option value="">Selecciona una opción</option>
                                <option value="Contado">Contado</option>
                                <option value="Credito">Credito</option>
                            </select>
                            <x-input-error for="cotizacion.formapago" />
                        </div>
                    </div>

                    <div>
                        <x-label for="observaciones">Comentarios</x-label>
                        <textarea wire:model="cotizacion.comentarios" class="w-full border rounded-lg p-2 mb-2" placeholder="Comentarios..."
                            id="observaciones" name="observaciones" rows="2"></textarea>
                        <x-input-error for="cotizacion.comentarios" />

                    </div>
                </div>
                <div class="modal-footer">

                    <x-button type="submit" id="GuardarProducto" class="ml-2"
                        wire:click="save">Guardar</x-button>
                </div>
            </div>
        </div>
    </div> --}}

    <x-dialog-modal wire:model="cotizacion.openEditarDetalle">
        <x-slot name="title">
            Editar Detalle Cotización
        </x-slot>
        <x-slot name="content">

            <div class="w-full">
                <x-label class="text-center"></x-label>

            </div>
            <div class="w-full">
                <x-label>Cantidad</x-label>
                <x-input wire:model="cotizacion.detalleEditar.cantidad" type="number" class="w-full" />
            </div>
            <div class="w-full">
                <x-label>Precio</x-label>
                <x-input wire:model="cotizacion.detalleEditar.precio" type="number" class="w-full" />
            </div>
            <div class="w-full">
                <x-label>Retencion % (0-100) </x-label>
                <x-input wire:model="cotizacion.detalleEditar.retencion" type="number" class="w-full" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="update()">Actualizar</x-button>
            <x-button wire:click="$set('cotizacion.openEditarDetalle',false)">Cancelar</x-button>
        </x-slot>
    </x-dialog-modal>

    <div class="modal fade" id="modalAddCotizacion" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Agregar Cotizacion</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="w-full mb-2">
                        <label for="select_proveedor">Proveedor</label>
                        <select wire:ignore class="w-full" name="cotizacion.proveedor_id" id="select_proveedor"
                            wire:model="cotizacion.proveedor_id" style="width: 100%!important">
                            <option value="select">Selecciona un proveedor</option>
                        </select>
                    </div>
                    <div class="w-full file-input-container" x-data="{ uploading: false, progress: 0, fileUploaded: false }"
                        x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false"
                        x-on:livewire-upload-error="uploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress">

                        <label for="cotizacion">Cotizacion</label>
                        <input wire:model.live="cotizacion.image" class="w-full" type="file" id="cotizacion"
                            name="cotizacion.image">
                        <!-- Progress Bar -->
                        <div class="w-full" x-show="uploading">
                            <progress class="w-full bg-red-200" max="100"
                                x-bind:value="progress"></progress>
                        </div>
                        {{-- <x-input-error for="cotizacion.image" /> --}}
                        <span class="font-danger fw-bold" id='error-file'></span>

                    </div>

                    <div style="overflow-y: auto; max-height: 30vh" class="mb-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" style="font-size: 0.9rem">Cantidad</th>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Retencion</th>
                                    <th scope="col">Precio</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requisicion->detalleRequisiciones as $detalle)
                                    <tr>

                                        <td class="col-2" style="font-size: 0.9rem;">{{ $detalle->cantidad }}</td>
                                        <td style="font-size: 0.8rem;">{{ $detalle->producto }}</td>
                                        <td class="col-2" style="font-size: 0.9rem;">
                                            <input wire:model="cotizacion.retenciones.{{ $detalle->id }}"
                                                value="1" placeholder="0-100" type="number" min=0 max=100
                                                class="form-control form-control-sm col-3">
                                        </td>
                                        <td class="col-3" style="font-size: 0.9rem;">
                                            <input wire:model="cotizacion.precios.{{ $detalle->id }}" value="1"
                                                type="number" class="form-control form-control-sm col-3">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row px-1 mb-2">
                        <div class="col-6 col-md-4">
                            <x-label for="cantidad">Tiempo de Entrega</x-label>
                            <input wire:model="cotizacion.dias_entrega"
                                class="form-control form-control-sm h-9 border rounded-lg mb-2" type="number"
                                id="cantidad" name="cantidad">
                            <x-input-error for="cotizacion.dias_entrega" />
                        </div>
                        <div class="col-6 col-md-4">
                            <x-label for="cantidad">Dias de Credito</x-label>
                            <input wire:model="cotizacion.dias_credito"
                                class="form-control form-control-sm h-9 border rounded-lg mb-2" type="number"
                                id="diascredito" name="diascredito">
                        </div>
                        {{--  <div class="col-6 col-md-4">
                            <x-label for="cantidad">% Retencion (0-100)</x-label>
                            <input wire:model="cotizacion.retencion"
                                class="form-control form-control-sm h-10 border rounded-lg mb-2" type="number"
                                id="retencion" name="retencion">
                        </div> --}}
                        <div class="col-6 col-md-4">
                            <x-label for="formaPago">Forma de Pago</x-label>
                            <select name="cotizacion.formapago" id="cotizacion.formapago"
                                wire:model.live="cotizacion.formapago"
                                class="form-control form-control-sm border-[#dee2e6] rounded-lg" id="formaPago">
                                <option value="">Metodo</option>
                                <option value="Contado">Contado</option>
                                <option value="Credito">Credito</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <x-label for="formaPago">Moneda</x-label>
                            <select name="cotizacion.moneda" id="cotizacion.moneda"
                                wire:model.live="cotizacion.moneda"
                                class="form-control form-control-sm border-[#dee2e6] rounded-lg" id="formaPago">
                                <option value="MXN">MXN$</option>
                                <option value="USD">$USD</option>
                            </select>
                        </div>

                    </div>

                    <div>
                        <x-label for="observaciones">Comentarios <span
                                id="contador_observaciones_cotizacion">0</span>/255</x-label>
                        <textarea wire:model="cotizacion.comentarios" class="form-control form-control-sm border w-100 rounded-lg p-2"
                            placeholder="Comentarios..." id="observaciones" name="cotizacion.comentarios" rows="2"></textarea>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        onclick="{resetDataAgregarCotizacion()}">Cancelar</button>
                    <button type="button" class="btn btn-primary" wire:click="save" id="btn_agregar_cotizacion"
                        wire:loading.attr="disabled">Agregar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="ModalEditProducto" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
        tabindex="-1" wire:ignore>
        <div class="modal-dialog modal-dialog-centered modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalToggleLabel">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <label> Productos </label>
                        <select class="w-full" id="producto_select" onchange="changeSelectProducto()"
                            style="width: 100%important;">
                            <option value="0" selected disabled>Seleccionar producto</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">

                    <x-button class="ml-2" onclick="updateProducto()">Guardar</x-button>
                </div>
            </div>
        </div>
    </div>


    <div>
        @livewire('producto.create')
    </div>

    <x-dialog-modal wire:model="openIncompleta">
        <x-slot name="title">
            Requisicion Incompleta
        </x-slot>
        <x-slot name="content">


            <div class="w-full">
                <x-label>Comentario</x-label>
                <textarea wire:model="comentario" class="w-full border rounded-lg p-2 mb-2" placeholder="Comentario..."
                    rows="4"></textarea>
                <x-input-error for="comentario" />
            </div>

        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="incompleta()" wire:loading.attr="disabled">Guardar</x-button>
            <x-button wire:click="$set('openIncompleta',false)" wire:loading.attr="disabled">Cancelar</x-button>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model="openPreAutorizacion">
        <x-slot name="title">
            Autorización
        </x-slot>
        <x-slot name="content">
            <p>Deje un comentario de cierre para autorizar.</p>

            <div class="w-full">
                <x-label>Comentario</x-label>
                <textarea wire:model="comentario_preautorizacion" id="input_preautorizacion"
                    class="w-full border rounded-lg p-2 mb-2" placeholder="Comentario..." rows="4"></textarea>
                <x-input-error for="comentario" />
                Caracteres <span id="contador_caracteres">0</span>/255
            </div>

        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="autorizarCotizacion()" id="btn-preautorizar" disabled
                wire:loading.attr="disabled">Autorizar</x-button>
            <x-button wire:click="$set('openPreAutorizacion',false)" wire:loading.attr="disabled">Cancelar</x-button>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model="openCotizacionUnicaComentario">
        <x-slot name="title">
            Autorización Cotizacion Unica
        </x-slot>
        <x-slot name="content">
            <p>Para finalizar Requisicion de "Cotizacion Unica" ingrese un breve comentario del porque se agrego una
                sola cotización.</p>

            <div class="w-full">
                <x-label>Comentario</x-label>
                <textarea wire:model="comentario_cotizacionunica" id="input_cotizacionunica"
                    class="w-full border rounded-lg p-2 mb-2" placeholder="Comentario..." rows="4"></textarea>
                <x-input-error for="comentario" />
                Caracteres <span id="contador_cotizacion_unica">0</span>/255
            </div>

        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="liberarRequisicionCotUnica()" wire:loading.attr="disabled" id="btn-autorizar-unica"
                disabled>FINALIZAR REQUISICIÓN</x-button>
            <x-button wire:click="$set('openCotizacionUnicaComentario',false)"
                wire:loading.attr="disabled">Cancelar</x-button>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model="openRemoveCotizacion">
        <x-slot name="title">
            Remover Cotizacion
        </x-slot>
        <x-slot name="content">
            <p>Se eliminara la siguiente cotizacion:</p>

        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="deleteCotizacion()" id="btn-remover-cotizacion"
                wire:loading.attr="disabled">Remover</x-button>
            <x-button wire:click="$set('openPreAutorizacion',false)" wire:loading.attr="disabled">Cancelar</x-button>
        </x-slot>
    </x-dialog-modal>



    @if ($requisicion->cotizaciones->count() >= $cantMinimaCotizaciones)
        @if ($requisicion->estatus_id == 5){{-- Estatus volver a cotizar --}}

            @if ($esCotizacionUnica)
                <x-button wire:click="$set('openCotizacionUnicaComentario',true)">Finalizar Requisición</x-button>
            @else
                <x-button wire:click="liberarRequisicion()" wire:loading.attr="disabled">Finalizar
                    Requisición</x-button>
            @endif

        @endif

        {{-- @if ($requisicion->estatus_id == 2)

            @if ($esCotizacionUnica)
                <x-button wire:click="$set('openCotizacionUnicaComentario',true)">Finalizar Requisición</x-button>
            @else
                <x-button wire:click="liberarRequisicion()" wire:loading.attr="disabled">Finalizar
                    Requisición</x-button>
            @endif

        @endif --}}

        @can('Incompleta', $requisicion)
            <div class="flex justify-between mt-4">
                <x-button wire:click="$set('openIncompleta',true)">Incompleta</x-button>
                @if ($esCotizacionUnica)
                    <x-button wire:click="$set('openCotizacionUnicaComentario',true)">Finalizar Requisición</x-button>
                @else
                    <x-button wire:click="liberarRequisicion()" wire:loading.attr="disabled">Finalizar
                        Requisición</x-button>
                @endif

            </div>
        @endcan
    @else
        <p class="text-center">Se necesitan {{ $cantMinimaCotizaciones }} Cotización para poder finalizar</p>
        <div class="d-flex justify-content-center">
            <x-button class="text-center" wire:click="$set('openIncompleta',true)">Incompleta</x-button>
        </div>

    @endif


    <div class="my-4 mx-2 flex justify-around">

        @can('autorizarCotizacion', $requisicion)
            <x-button wire:click="$toggle('openIncompleta')"
                class="bg-red-500 hover:bg-red-400 active:bg-red-300 focus:bg-red-400">Volver a cotizar</x-button>
            <x-button wire:click="$toggle('openPreAutorizacion')"
                class="bg-green-500 hover:bg-green-400 active:bg-green-300 focus:bg-green-400">Autorizar</x-button>
            {{-- <x-button wire:click="autorizarCotizacion()"
                class="bg-green-500 hover:bg-green-400 active:bg-green-300 focus:bg-green-400">Autorizar</x-button> --}}
        @endcan


    </div>


    <script>
        var contadorCaracteresComentario = 0;
        var comentPreAutorizar = ''
        var comentCotUnica = ''
        var valueProveedorSelected = ''
        var proveedorSelected = {
            id: '',
            name: '',
        }

        var productoSelected = {
            id: 0,
            name: '',
            detalle_id: 0,
        }

        var allProveedores = @json($proveedores)

        function resetDataAgregarCotizacion() {
            $('#contador_observaciones_cotizacion').text('0');
            $('#select_proveedor').val('select').trigger('change');
            $('#observaciones').val('');
        }

        function InicializarSelect() {
            console.log('lala');

            $('.select2').select2({
                dropdownParent: $("#ModalEditProducto")
            });
            $('.select2').select2({
                dropdownParent: $("#ModalAddCotizacion")
            });


            $('#producto_select').select2({
                dropdownParent: $("#ModalEditProducto")
            })
        }

        InicializarSelect()

        function llenarSelectProductos(productos) {
            const select = document.getElementById('producto_select');
            select.innerHTML = '<option value="0" selected disabled>Seleccionar producto</option>';
            productos.forEach(producto => {
                const option = document.createElement('option');
                option.value = producto.cidproducto; // Usamos el ID del producto como valor
                option.textContent = producto.cnombreproducto; // Usamos el nombre del producto como texto
                select.appendChild(option);
            });
        }

        function getListaProductosSucursal(id) {
            productoSelected.detalle_id = id

            $.ajax({
                url: window.location.origin + `/productos/asignar?ri={{ $requisicion->id }}`, // URL del endpoint
                type: 'GET', // Método HTTP
                dataType: 'json', // Tipo de datos esperados
                success: function(response) {
                    // Código a ejecutar si la solicitud es exitosa
                    llenarSelectProductos(response);
                    console.log(response);

                },
                error: function(xhr, status, error) {
                    // Código a ejecutar si hay un error
                    console.error('Error en la solicitud:', error);
                }
            });
        }

        function changeSelectProducto() {
            console.log($('#producto_select'));

            var selectInputVal = $('#producto_select').val();
            var selectInputText = $('#producto_select').find('option:selected').text();

            productoSelected.id = selectInputVal;
            productoSelected.name = selectInputText;

        }


        function updateProducto() {
            console.log('value selected');
            console.log(productoSelected);

            @this.call('updateProducto', productoSelected.id, productoSelected.name, productoSelected.detalle_id);

        }

        function resetInputFile() {
            //$(`#selectProveedor option[value="${valueProveedorSelected}"]`).remove();
            let numero = Math.floor(Math.random() * 10);
            //console.log(numero);
            var container = document.querySelector('.file-input-container');
            var inputElement = document.getElementById("cotizacion");

            // Eliminar el input existente
            if (inputElement) {
                inputElement.remove();
            }

            // Crear un nuevo input de tipo file
            var newInputElement = document.createElement('input');
            newInputElement.setAttribute('type', 'file');
            newInputElement.setAttribute('id', 'cotizacion');
            newInputElement.classList.add('w-full');
            newInputElement.setAttribute('wire:model.live', 'image');
            newInputElement.setAttribute('wire:key', numero); // Aquí podrías establecer un nuevo valor único

            // Insertar el nuevo input después del contenedor
            container.parentNode.insertBefore(newInputElement, container.nextSibling);

            location.reload()
        }

        function cerrarModalEditProducto() {
            var closeButton = $('#ModalEditProducto .btn-close'); // Busca el botón de cierre dentro del modal
            closeButton.click();
            //InicializarSelect()
            //window.location.reload()
        }

        function cerrarModalAddCotizacion() {
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            $('#cotizacion').val('')
            $('#modalAddCotizacion').modal('hide');
            $("#select_proveedor").prop('selectedIndex', 0)
            proveedorSelected.id = ''
            proveedorSelected.name = ''

            @this.set('cotizacion.proveedor_id', '');
            @this.set('cotizacion.proveedor', '');
        }

        function renderOptionsSelectProveedores(proveedores) {
            $("#select_proveedor").empty().append('<option value="select">Seleccione un Proveedor</option>');
            $.each(proveedores, function(i, item) {
                $('#select_proveedor').append($('<option>', {
                    value: item.cidclienteproveedor,
                    text: item.crazonsocial
                }))
            });
        }

        function mostrarErrores(errors) {
            document.querySelectorAll('.error-message').forEach(el => el.remove());

            Object.keys(errors).forEach((key) => {
                //console.log(key);
                const field = document.querySelector(`[name="${key}"]`);

                if (field) {
                    const errorMessage = document.createElement('span');
                    errorMessage.classList.add('error-message');
                    errorMessage.style.color = 'red'; // Puedes estilizarlo como desees
                    errorMessage.style.fontSize = '0.8rem';
                    errorMessage.style.whiteSpace = 'pre';
                    errorMessage.innerText = errors[key][0]; // Mostrar solo el primer mensaje de error

                    field.parentNode.insertBefore(errorMessage, field.nextSibling);
                }
            });
        }

        document.getElementById('producto_select').addEventListener('change', function(event) {
            const selectedValue = event.target.value; // Obtiene el valor seleccionado
            console.log("Producto seleccionado:", selectedValue);
        });

        document.addEventListener('renderProveedores', event => {
            const proveedores = event.detail[0].proveedores; // Aquí obtienes la lista de productos
            renderOptionsSelectProveedores(proveedores);
        });

        document.addEventListener('cerrar-modal', event => {
            cerrarModalAddCotizacion();
        });

        document.addEventListener('nuevo_proveedores', event => {
            //console.log(event.detail[0].proveedores);
            allProveedores = event.detail[0].proveedores
            renderOptionsSelectProveedores(allProveedores)
        });

        document.addEventListener('validate-errors', event => {
            var errores = event.detail[0].errors
            //console.log(errores);
            mostrarErrores(errores)
        });


        $(document).on('change', '.form-check-input', function(e) {
            //console.log('algo cambió');
            var isChecked = $(this).is(':checked');
            //console.log(isChecked);
        });

        $(document).on('cerrar-modal-edit-producto', function() {
            cerrarModalEditProducto();
            Livewire.dispatch('Actualizardetalle');
        });

        $(document).on('cerrar-modal-add-cotizacion', function() {
            //console.log('se cerro modal');

            resetInputFile();
            cerrarModalAddCotizacion();

        });

        $('#select_proveedor').select2({
            dropdownParent: $("#modalAddCotizacion")
        });

        $('#select_proveedor').on('change', function(e) {
            var provId = e.target.value;
            var provText = e.target.options[e.target.selectedIndex].text;


            proveedorSelected.id = provId
            proveedorSelected.name = provText
            @this.set('cotizacion.proveedor_id', proveedorSelected.id);
            @this.set('cotizacion.proveedor', proveedorSelected.name);

        })

        $('#observaciones').on('keyup', function(e) {
            let comentarioCoti = e.target.value;
            $('#contador_observaciones_cotizacion').text(comentarioCoti.length);
            if (comentarioCoti === '' || comentarioCoti.length > 255) {
                //console.log('Comentario obligarotiros');
                $('#btn_agregar_cotizacion').prop('disabled', true);
                $('#btn_agregar_cotizacion').hide();
                //alert('Comentario para finalizar requisicion con Cotizacion Unica es obligatorio.')
            } else {
                //console.log('coment valido');
                $('#btn_agregar_cotizacion').prop('disabled', false);
                $('#btn_agregar_cotizacion').show();
            }

        })

        $('#input_cotizacionunica').on('keyup', function(e) {
            console.log('algo cambio');
            let cotizacionUnica = e.target.value
            $('#contador_cotizacion_unica').text(cotizacionUnica.length);
            if (cotizacionUnica === '' || cotizacionUnica.length > 255) {
                //console.log('Comentario obligarotiros');
                $('#btn-autorizar-unica').prop('disabled', true);
                $('#btn-autorizar-unica').hide();
            } else {
                //console.log('coment valido');
                $('#btn-autorizar-unica').prop('disabled', false);
                $('#btn-autorizar-unica').hide();
            }

        })


        $('#input_preautorizacion').on('input', function(e) {
            let comentario_preauto = e.target.value

            $('#contador_caracteres').text(comentario_preauto.length);

            if (comentario_preauto === '' || comentario_preauto.length > 255) {
                console.log('Comentario obligarotiros');
                $('#btn-preautorizar').prop('disabled', true);
                $('#btn-preautorizar').hide();
            } else {
                $('#btn-preautorizar').prop('disabled', false);
                $('#btn-preautorizar').show();
            }

        });

        /* $('#input_preautorizacion').on('keyup', function(e) {

            comentPreAutorizar = e.target.value
            if (comentPreAutorizar === '' && comentPreAutorizar.length < 255) {
                //console.log('Comentario obligarotiros');
                $('#btn-preautorizar').prop('disabled', true);
            } else {
                //console.log('coment valido');
                $('#btn-preautorizar').prop('disabled', false);
            }

        }) */

        renderOptionsSelectProveedores(allProveedores)

        $(document).ready(function() {
            $('#producto_select').select2({
                dropdownParent: $("#ModalEditProducto")
            });

            document.getElementById('producto_select').addEventListener('change', function(event) {
                const selectedValue = event.target.value; // Obtiene el valor seleccionado
                console.log("Producto seleccionado:", selectedValue);
            });


            $('#selectProveedor').on('change', function(e) {
                //console.log(e.target.value);
                valueProveedorSelected = e.target.value
                @this.set('cotizacion.proveedor_id', $(this).val());
                @this.set('cotizacion.proveedor', $(this).find('option:selected').text());


            });

            $(document).ready(function() {


                document.getElementById('producto_select').addEventListener('change', function(event) {
                    const selectedValue = event.target.value; // Obtiene el valor seleccionado
                    console.log("Producto seleccionado:", selectedValue);
                });
                $('#SelectProducto').on('change', function() {
                    @this.set('producto.id_Producto', $(this).val());
                    @this.set('producto.producto', $(this).find('option:selected').text());


                });
                $('#input_cotizacionunica').on('keyup', function(e) {
                    console.log('algo cambio');

                    comentPreAutorizar = e.target.value
                    if (comentPreAutorizar === '') {
                        //console.log('Comentario obligarotiros');
                        $('#btn-autorizar-unica').prop('disabled', true);
                        alert(
                            'Comentario para finalizar requisicion con Cotizacion Unica es obligatorio.'
                        )
                    } else {
                        //console.log('coment valido');
                        $('#btn-autorizar-unica').prop('disabled', false);
                    }

                })
            });

            /* $('#ModalEditProducto').on('shown.bs.modal', function() {
                $('.select2').select2({
                    dropdownParent: $("#ModalEditProducto")
                });
            }); */

            $('#ModalAddCotizacion').on('shown.bs.modal', function() {
                $('.select2').select2({
                    dropdownParent: $("#ModalAddCotizacion")
                });
            });

            // Desde Livewire, cerrar el modal
            Livewire.on('uncheckCotizacionUnica', function() {
                $('#cotizacion_unica').prop('checked', false);
            });

            /* Livewire.on('togglemodal', function() {
                $('#ModalEditProducto').modal('show');
            }); */



        });
    </script>

    <style>
        @media (max-width: 768px) {
            .hide-text-btn {
                display: none;
            }

            .text-proveedor {
                text-align: start !important;
                justify-content: right;
            }
        }
    </style>


</div>
