<div class="p-4 border rounded mb-4">
    <div class="w-full md:flex md:justify-between md:items-center">


        <div class="flex flex-col">
            <span class="text-sm text-gray-500"> Fecha Creacion {{ \Carbon\Carbon::parse($requisicion->created_at)->locale('es')->isoFormat('LL') }}</span>
            <p class="text-gray-500 text-md font-bold">Folio: {{ $requisicion->folio }}</p>
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
        <p class="text-gray-500 text-md">
            Fecha Requerida: {{ \Carbon\Carbon::parse($requisicion->fecharequerida)->locale('es')->isoFormat('LL') }}
            ({{ \Carbon\Carbon::parse($requisicion->fecharequerida)->locale('es')->diffForHumans() }})
        </p>

    </div>

    <p class="text-gray-500 text-md">Observaciones: {{ $requisicion->observaciones }}</p>

    <div class="flex justify-between items-center">

        <x-button wire:click="$set('openComentarios',true)" class="my-2" >Ver Comentarios</x-button>

        <button type="button" class="btn btn-primary" id="addProduct" data-bs-toggle="modal" data-bs-target="#myModal">
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
                <tr class="text-center @if($detalle->producto_id == 0) bg-red-200 @endif">
                    <td class="md:py-2 md:px-4 border-b">{{ $detalle->cantidad }}</td>
                    <td class="md:py-2 md:px-4 border-b">{{ $detalle->producto }}</td>
                    <td class="md:py-2 md:px-4 border-b">{{ $detalle->observaciones }}</td>
                    <td>
                        <x-danger-button wire:click="deleteProduct({{ $detalle->id }})">

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                            </svg>
                        </x-danger-button>


                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <div wire:ignore class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

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



    </div>

    <div class="mt-4 mb-1 text-end">
        <x-button  wire:click="finalizarIncompleta()">Finalizar</x-button>
    </div>




</div>

<script>
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

    function cerrarModal() {
        var closeButton = document.querySelector('.btn-close');
        closeButton.click();
    }


    document.addEventListener("livewire:load", function(event) {
        initializeSelect2();
    });

    $(document).ready(function() {
        // Inicializar Select2 al cargar la página

        // Se ejecuta cuando se muestra el modal
        $('#myModal').on('shown.bs.modal', function() {
            initializeSelect2();
        });

        $('#myModalEdit').on('shown.bs.modal', function() {
            initializeSelect22();
        });

        // Desde Livewire, cerrar el modal


        $('#productoSelect').on('change', function() {

            @this.set('producto.producto_id', $(this).val());
            @this.set('producto.producto', $(this).find('option:selected').text());


        });


        $('#productoNoRegistrado').change(function() {
            // Verifica si el checkbox está seleccionado
            if ($(this).is(':checked')) {
                @this.set('producto.producto', "");
                @this.set('producto.producto_id', 0);
            }
        });

    });
</script>

</div>