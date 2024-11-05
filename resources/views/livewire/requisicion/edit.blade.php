<div class="p-4 border rounded mb-4">
    <div class="w-full md:flex md:justify-between md:items-center">


        <div class="flex flex-col">
            <span class="text-sm text-gray-500"> Fecha Creacion
                {{ \Carbon\Carbon::parse($requisicion->created_at)->locale('es')->isoFormat('LL') }}</span>
            <p class="text-gray-500 text-md font-bold">Folio: {{ $requisicion->folio }}</p>
        </div>

        @if ($requisicion->evidencia()->exists())
            <button wire:click.prevent="downloadevidencia({{ $requisicion->evidencia()->first()->id }})"
                class=" relative inline-flex items-center  px-1 py-2 text-sm font-medium rounded-lgfocus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 ">
                <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path
                        d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z" />
                    <path
                        d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                </svg>
                Evidencia
            </button>
        @endif
    </div>


    <div class="md:flex md:justify-between ">
        <p class="text-gray-500 text-md">Solicitante: {{ $requisicion->solicitante->getNombreCompleto() }}</p>
        <p class="text-gray-500 text-md">
            Fecha Requerida: {{ \Carbon\Carbon::parse($requisicion->fecharequerida)->locale('es')->isoFormat('LL') }}
            ({{ \Carbon\Carbon::parse($requisicion->fecharequerida)->locale('es')->diffForHumans() }})
        </p>

    </div>

    <p class="text-gray-500 text-md">Observaciones: {{ $requisicion->observaciones }}</p>

    <div class="flex justify-between items-center">

        <x-button wire:click="$set('openComentarios',true)" class="my-2">Ver Comentarios</x-button>

        <button type="button" class="btn btn-primary" id="addProduct" data-bs-toggle="modal"
            data-bs-target="#modalAddProd">
            Agregar Producto
        </button>

    </div>



    <livewire:requisicion.component.comentarios :requisicion="$requisicion" wire:model="openComentarios" />



    <div class="w-full text-sm relative ">
        <table class=" w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-center ">
                    <th class="md:py-2 md:px-4 border-b ">Cantidad</th>
                    <th class="md:py-2 md:px-4 border-b ">Producto</th>
                    <th class="md:py-2 md:px-4 border-b ">Observaciones</th>
                    <th class="md:py-2 md:px-4 border-b "></th>

                    <!-- Agrega más columnas según tus necesidades -->
                </tr>
            </thead>
            <tbody>

                @foreach ($requisicion->detalleRequisiciones as $detalle)
                    <tr class="text-center @if ($detalle->producto_id == 0) bg-red-200 @endif">
                        <td class="md:py-2 md:px-4 border-b">{{ $detalle->cantidad }}</td>
                        <td class="md:py-2 md:px-4 border-b">{{ $detalle->producto }}</td>
                        <td class="md:py-2 md:px-4 border-b">{{ $detalle->observaciones }}</td>
                        <td>
                            <x-danger-button wire:click="deleteProduct({{ $detalle->id }})">

                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path
                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                    <path
                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                </svg>
                            </x-danger-button>


                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    {{-- <div wire:ignore class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detalle Productos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div x-data="{open:true}">
                        <div x-show="open">
                            <x-label for="">Producto</x-label>

                            <select class="select2 w-full" id="productoSelect" required>
                                <option value="" selected disabled>Seleccionar producto</option>
                                @foreach ($productos as $producto)
                                <option value="{{$producto['cidproducto']}}">{{$producto['cnombreproducto']}}</option>
                                @endforeach
                            </select>
                            <x-input-error for="producto.producto" />
                        </div>

                        <div x-show="!open">
                            <x-label for="">Producto no registrado en contpaqi Comercial</x-label>
                            <x-input wire:model="producto.producto" class="w-full"></x-input>
                            <x-input-error for="producto.producto" />
                        </div>


                        <x-checkbox id="productoNoRegistrado" x-on:click="open = ! open">Ninguno</x-checkbox>

                    </div>

                    <div>
                        <x-label for="cantidad">Cantidad</x-label>
                        <input wire:model="producto.cantidad" class="w-full h-10 border rounded-lg mb-2" type="number" id="cantidad" name="cantidad" required>
                        <x-input-error for="producto.cantidad" />
                    </div>

                    <div>
                        <x-label for="observaciones">Observaciones</x-label>
                        <textarea wire:model="producto.observaciones" class="w-full border rounded-lg p-2 mb-2" placeholder="Observaciones..." id="observaciones" name="observaciones" rows="4" required></textarea>


                    </div>



                    <div>

                        <x-label for="existencias">Total de existencias</x-label>
                        <input disabled wire:model.live="existencias" class="w-full h-10 border rounded-lg mb-2" type="number" id="existencias" name="existencias">

                    </div>


                </div>
                <div class="modal-footer">
                    <x-button wire:click="addProducto" class="ml-2 bg-blue-500 hover:bg-blue-700">Guardar</x-button>
                </div>

            </div>
        </div>



    </div> --}}

    <div class="modal fade" id="modalAddProd" tabindex="-1" aria-labelledby="modalAddProd" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detalle Productos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div wire:ignore id="container-select2">
                        <label> Producto </label>
                        <select class="w-full" id="producto_select" style="width: 100%important;">
                            <option value="0" selected disabled>Seleccionar producto</option>
                        </select>
                    </div>
                    <div id="container-input-nproduct" style="display: none">
                        <x-label for="">Producto no registrado en contpaqi Comercial</x-label>
                        <x-input id="inputValueNewProduct" class="w-full"></x-input>
                    </div>

                    <x-checkbox id="productoNoRegistrado" onclick="showNewProduct()" wire:model="producto.producto" />
                    Ninguno

                    <div>
                        <x-label for="cantidad">Cantidad</x-label>
                        <input wire:model="requisicion.producto.cantidad" class="w-full h-10 border rounded-lg mb-2"
                            type="number" id="cantidad" name="cantidad" required>
                        <x-input-error for="producto.cantidad" />
                    </div>

                    <div>
                        <x-label for="observaciones">Observaciones</x-label>
                        <textarea wire:model="producto.observaciones" class="w-full border rounded-lg p-2 mb-2" placeholder="Observaciones..."
                            id="observaciones" name="observaciones" rows="4" required></textarea>
                        <x-input-error for="producto.observaciones" />

                    </div>



                    <div>

                        <x-label for="existencias">Total de existencias</x-label>
                        <input disabled wire:model.live="existencias" class="w-full h-10 border rounded-lg mb-2"
                            type="number" id="existencias" name="existencias">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="sendAddProduct()">Agregar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 mb-1 text-end">
        <button class="btn btn-dark" onclick="sendFinalizarIncompleta()">FINALIZAR</button>
    </div>

</div>

<script>
    var showProductos = true;

    $('#producto_select').select2({
        dropdownParent: $("#modalAddProd")
    });

    var valDetalleProductosEx = {
        'producto_id': '',
        'producto': '',
    }

    var valDetalleProductosIn = {
        'producto_id': '',
        'producto': '',
    }

    function getExistencias() {
        var id = valDetalleProductosEx.producto_id !== '' ? valDetalleProductosEx.producto_id : valDetalleProductosIn
            .producto_id
        var nom = '{{ $sucursal->nomenclatura }}'
        $.ajax({
            url: `${window.location.origin}/${nom}/producto/${id}/existencia`, // URL de la API o recurso
            type: 'GET',
            dataType: 'json', // El tipo de datos que esperas recibir (puede ser 'json', 'text', etc.)
            success: function(response) {
                $('#existencias').val(response);
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', status, error);
            }
        });
    }

    function showNewProduct() {
        if (showProductos) {
            showProductos = !showProductos
            $('#container-select2').hide()
            $('#container-input-nproduct').show()
        } else {
            showProductos = !showProductos
            $('#container-select2').show()
            $('#container-input-nproduct').hide()
        }
    }

    function cerrarModal() {
        $('#modalAddProd').modal('hide');
        showProductos = true
        $('#container-select2').show()
        $('#container-input-nproduct').hide()
        $('#observaciones').val('');
        $('#cantidad').val(0);

        var closeButton = document.querySelector('.btn-close');
        closeButton.click();
        valDetalleProductosIn.producto_id = ''
        valDetalleProductosIn.producto = ''
        valDetalleProductosEx.producto_id = ''
        valDetalleProductosEx.producto = ''
    }

    function sendAddProduct() {
        var observacion = $('#observaciones').val();
        var cantidad = $('#cantidad').val();
        var data;
        if ($('#productoNoRegistrado').is(':checked')) {
            data = {
                producto_id: valDetalleProductosIn.producto_id,
                producto: valDetalleProductosIn.producto,
                observaciones: observacion,
                cantidad: cantidad
            }
            @this.call('addProducto', data);
        } else {
            data = {
                producto_id: valDetalleProductosEx.producto_id,
                producto: valDetalleProductosEx.producto,
                observaciones: observacion,
                cantidad: cantidad
            }

            @this.call('addProducto', data);

        }

    }

    function sendFinalizarIncompleta() {
        @this.call('finalizarIncompleta');
    }

    function renderOptionsSelect2(productos) {
        $("#producto_select").empty().append('<option value="0">Seleccione un Producto</option>');
        $.each(productos, function(i, item) {
            $('#producto_select').append($('<option>', {
                value: item.cidproducto,
                text: item.cnombreproducto
            }))
        });
    }

    function initializeSelect2() {
        $('.select2').select2({
            dropdownParent: $("#myModal")
        });


    }

    function initializeSelect22() {
        $('.select2').select2({
            dropdownParent: $("#myModalEdit")
        });


    }

    document.addEventListener('renderProductos', event => {
        const productos = event.detail[0].productos; // Aquí obtienes la lista de productos

        renderOptionsSelect2(productos);
    });

    document.addEventListener('cerrar-modal', event => {
        cerrarModal();
    });

    $('#producto_select').on('change', function(e) {
        var productoId = e.target.value;
        var productoText = e.target.options[e.target.selectedIndex].text;

        valDetalleProductosEx.producto_id = productoId
        valDetalleProductosEx.producto = productoText
        getExistencias()
    });

    $('#inputValueNewProduct').on('keyup', function(e) {
        valDetalleProductosIn.producto = e.target.value
    })


    $('#productoNoRegistrado').change(function(e) {
        if ($('#productoNoRegistrado').is(':checked')) {
            valDetalleProductosIn.producto = ''
            valDetalleProductosIn.producto_id = 0
            $('#inputValueNewProduct').val('')
        } else {
            console.log('producto si existe');
        }

    });


    /* document.addEventListener("livewire:load", function(event) {
        initializeSelect2();
    }); */

    $(document).ready(function() {
        $('#producto_select').select2({
            dropdownParent: $("#modalAddProd")
        });
        $('#producto_select').on('change', function(e) {

            var productoId = e.target.value;
            var productoText = e.target.options[e.target.selectedIndex].text;

            valDetalleProductosEx.producto_id = productoId
            valDetalleProductosEx.producto = productoText

        });
        $('#productoNoRegistrado').change(function(e) {
            if ($('#productoNoRegistrado').is(':checked')) {
                valDetalleProductosIn.producto = ''
                valDetalleProductosIn.producto_id = 0
                $('#inputValueNewProduct').val('')
            } else {
                console.log('producto si existe');

            }
        });
    });
</script>

</div>
