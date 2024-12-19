<div>

    <form wire:submit.prevent="save">
        <div class="md:flex md:space-x-2 mb-2">
            <div class="mb-2 w-full">
                <x-label for="">Empresa</x-label>
                <select wire:model.live="requisicion.empresa_id"
                    class="border-1 border-slate-900 rounded-md w-full   text-black text-md capitalize">
                    <option value="" selected disabled>
                        Seleccione una empresa
                    </option>
                    @foreach ($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->name }}</option>
                    @endforeach
                </select>
                <x-input-error for="requisicion.empresa_id" />
            </div>
            <div class="mb-2 w-full">
                <x-label for="">Sucursal</x-label>
                <select wire:model.live="requisicion.sucursal_id" id="idsucursal"
                    class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                    <option value="" selected disabled>
                        Seleccione una sucursal
                    </option>

                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">
                            {{ $sucursal->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error for="requisicion.sucursal_id" />
            </div>
            <div class="mb-2 w-full">
                <x-label for="">Fecha Requerida</x-label>
                <input wire:model='requisicion.fecharequerida'
                    class="w-full border-1 border-slate-900 rounded-md  text-black text-md " type="date"
                    id="fechaDocumento" name="fechaDocumento" />
                <x-input-error for="requisicion.fecharequerida" />
            </div>
            <div class="mb-2 w-full">
                <x-label for="">Proyecto</x-label>
                <select wire:model.live="requisicion.proyecto_id" id="proyectoSelect"
                    class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                    <option value="" selected disabled>
                        Seleccione un proyecto
                    </option>

                    @foreach ($proyectos as $proyecto)
                        <option value="{{ $proyecto['cidproyecto'] }}">
                            {{ $proyecto['cnombreproyecto'] }}
                        </option>
                    @endforeach
                </select>
                <x-input-error for="requisicion.proyecto_id" />

            </div>
            <div class="mb-2 w-full">
                <x-label for="">Solicitante</x-label>
                <select wire:model.live="requisicion.empleado_id" id="requisicion.empleado_id"
                    class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                    <option value="" selected disabled>
                        Seleccione un solicitante
                    </option>

                    @foreach ($solicitantes as $solicitante)
                        <option value="{{ $solicitante->id }}">
                            {{ $solicitante->getNombreCompleto() }}
                        </option>
                    @endforeach
                </select>

            </div>
        </div>




        <div class="flex items-center">
            {{-- <button type="button" @if (!$productoscargados) disabled @endif class="btn btn-primary"
                id="addProduct" data-bs-toggle="modal" data-bs-target="#myModal">
                Agregar Producto
            </button> --}}

            <button @if (!$productoscargados) disabled @endif wire:loading.attr="disabled" type="button"
                class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddProd">
                Agregar Producto
            </button>

        </div>




        <div class="mt-4 mb-4 bg-white  rounded-lg shadow-md">
            @if (!empty($requisicion->listaProductos))
                <table class="w-full shadow-lg">
                    <thead class="bg-green-300">
                        <tr>
                            <th class="p-2 sm:p-1">Producto</th>
                            <th class="p-2 sm:p-1">Cantidad</th>
                            <th class="p-2 sm:p-1">Observaciones</th>
                            <th class="p-2 sm:p-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requisicion->listaProductos as $index => $producto)
                            <tr>

                                <th class="p-2 sm:p-1">{{ $producto['producto'] }}</th>
                                <th class="p-2 sm:p-1">{{ $producto['cantidad'] }}</th>
                                <th class="p-2 sm:p-1">{{ $producto['observaciones'] }}</th>
                                <th class="p-2 sm:p-1">
                                    <x-danger-button
                                        wire:click="deleteProducto({{ $index }}, '{{ $producto['producto'] }}')">X</x-danger-button>
                                </th>
                            </tr>
                        @endforeach


                    </tbody>
                </table>
            @endif
        </div>


        <div class="mb-2">
            <x-label for="">Observaciones</x-label>
            <textarea wire:model="requisicion.observaciones" class="w-full border rounded-lg p-2" placeholder="Observaciones..."
                id="observaciones" name="observaciones" rows="4"></textarea>
            <x-input-error for="requisicion.observaciones" />
            <x-input-error for="requisicion.listaProductos" />
        </div>






        <div class="flex items-center space-x-4 mb-4">
            <x-checkbox wire:click="toggleOpenUnidades" id="unidadTaller" />
            <x-label for="unidadTaller">¿Es para unidad de Taller?</x-label>

            @if ($openUnidades)
                <div class="mb-2 w-1/2 "> <!-- Ajusta el ancho según tus necesidades -->
                    <x-label for="">Unidades</x-label>
                    <select wire:model.live="requisicion.unidad"
                        class="border-1 border-slate-900 rounded-md w-full text-black text-md capitalize">
                        <option value="" selected disabled>Seleccionar unidad</option>
                        @if ($unidades)
                            @foreach ($unidades as $unidad)
                                <option value="{{ $unidad['numeroEconomico'] }}">{{ $unidad['numeroEconomico'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <x-input-error for="requisicion.unidad" />
                </div>
            @endif

        </div>

        <div class="flex items-center space-x-4 mb-4">
            <x-checkbox wire:model="requisicion.seguimiento" type="checkbox" id="Seguimiento" />
            <x-label for="Seguimiento">Seguimiento por correo electrónico?</x-label>
        </div>


        <div class="w-full" x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress">
            <!-- File Input -->
            <x-label for="">Evidencia</x-label>
            <input wire:model="requisicion.image" class="w-full" type="file">
            <!-- Progress Bar -->
            <div class="w-full" x-show="uploading">
                <progress class="w-full " max="100" x-bind:value="progress">

                </progress>
            </div>
        </div>



        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4">Crear</button>

    </form>


    {{-- <div wire:ignore.self class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detalle Productos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div x-data="{ showProductos: $wire.entangle('requisicion.openProductoSR') }">
                        <div x-show="showProductos">
                            <x-label for="">Producto</x-label>

                            <select wire:ignore wire:model.live="requisicion.producto.producto_id"
                                class="select2 w-full" id="productoSelect" required>
                                <option value="" selected disabled>Seleccionar producto</option>
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto['cidproducto'] }}">
                                        {{ $producto['cnombreproducto'] }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="requisicion.producto.producto_id" />
                        </div>

                        <div x-show="!showProductos">
                            <x-label for="">Producto no registrado en contpaqi Comercial</x-label>
                            <x-input id="inputValueNewProduct" class="w-full"></x-input>
                        </div>

                        <x-checkbox id="productoNoRegistrado" wire:model="requisicion.productosinregistro"
                            x-on:click="showProductos = !showProductos" /> Ninguno
                    </div>


                    <div>
                        <x-label for="cantidad">Cantidad</x-label>
                        <input wire:model="requisicion.producto.cantidad" class="w-full h-10 border rounded-lg mb-2"
                            type="number" id="cantidad" name="cantidad" required>
                        <x-input-error for="requisicion.producto.cantidad" />
                    </div>

                    <div>
                        <x-label for="observaciones">Observaciones</x-label>
                        <textarea wire:model="requisicion.producto.observaciones" class="w-full border rounded-lg p-2 mb-2"
                            placeholder="Observaciones..." id="observaciones" name="observaciones" rows="4" required></textarea>
                        <x-input-error for="requisicion.producto.observaciones" />

                    </div>



                    <div>

                        <x-label for="existencias">Total de existencias</x-label>
                        <input disabled wire:model.live="existencias" class="w-full h-10 border rounded-lg mb-2"
                            type="number" id="existencias" name="existencias">

                    </div>


                </div>
                <div class="modal-footer">
                    <x-button onclick="sendAddProduct()" class="ml-2 bg-blue-500 hover:bg-blue-700">Guardar</x-button>
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
                        <select class="w-full" id="producto_select" style="width: 100%important;"
                            wire:model="requisicion.producto.producto_id">
                            <option value="0" selected disabled>Seleccionar producto</option>
                        </select>
                    </div>
                    <div id="container-input-nproduct" style="display: none">
                        <x-label for="">Producto no registrado en contpaqi Comercial</x-label>
                        <x-input id="inputValueNewProduct" class="w-full"></x-input>
                    </div>

                    <x-checkbox id="productoNoRegistrado" onclick="showNewProduct()"
                        wire:model="requisicion.productosinregistro" /> Ninguno

                    <div>
                        <x-label for="cantidad">Cantidad</x-label>
                        <input wire:model="requisicion.producto.cantidad" class="w-full h-10 border rounded-lg mb-2"
                            type="number" id="cantidad" name="cantidad" required>
                        <x-input-error for="requisicion.producto.cantidad" />
                    </div>

                    <div>
                        <x-label for="observaciones">Observaciones</x-label>
                        <textarea wire:model="requisicion.producto.observaciones" class="w-full border rounded-lg p-2 mb-2"
                            placeholder="Observaciones..." id="observaciones" name="observaciones" rows="4" required></textarea>
                        <x-input-error for="requisicion.producto.observaciones" />

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

        function initializeSelect2() {
            $('.select2').select2({
                dropdownParent: $("#myModal")
            });

        }

        function cerrarModal() {
            $('#modalAddProd').modal('hide');
            showProductos = true
            $('#container-select2').show()
            $('#container-input-nproduct').hide()
            
            var closeButton = document.querySelector('.btn-close');
            closeButton.click();
            valDetalleProductosIn.producto_id = ''
            valDetalleProductosIn.producto = ''
            valDetalleProductosEx.producto_id = ''
            valDetalleProductosEx.producto = ''
        }


        function sendAddProduct() {
            if ($('#productoNoRegistrado').is(':checked')) {
                @this.call('addProducto', valDetalleProductosIn.producto_id, valDetalleProductosIn.producto);
            } else {
                @this.call('addProducto', valDetalleProductosEx.producto_id, valDetalleProductosEx.producto);

            }

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


        document.addEventListener('renderProductos', event => {
            const productos = event.detail[0].productos; // Aquí obtienes la lista de productos
            /* console.log(event);
            console.log(productos); */
            renderOptionsSelect2(productos);

            // Si deseas procesar los productos en el DOM o realizar alguna acción
            /* productos.forEach(producto => {
                console.log(`Producto: ${producto.cnombreproducto} - id: ${producto.cidproducto}`);
            }); */
        });

        document.addEventListener('cerrar-modal', event => {
            cerrarModal();
        });

        $('#producto_select').on('change', function(e) {
            /* console.log('cambio en select');
            console.log(e.target.options[e.target.selectedIndex].text); */
            var productoId = e.target.value;
            var productoText = e.target.options[e.target.selectedIndex].text;

            valDetalleProductosEx.producto_id = productoId
            valDetalleProductosEx.producto = productoText
            /* @this.set('requisicion.producto.producto_id', $(this).val());
            @this.set('requisicion.producto.producto', $(this).find('option:selected').text()); */
            //@this.call('valueSelectProductChange', {producto_id: productoId, producto: productoText});
            /*  @this.set('requisicion.producto.producto_id', $(this).val());
             @this.set('requisicion.producto.producto', $(this).find('option:selected').text()); */


        });

        $('#inputValueNewProduct').on('keyup', function(e) {
            valDetalleProductosIn.producto = e.target.value
        })


        $('#productoNoRegistrado').change(function(e) {
            /* console.log(e.target); */
            if ($('#productoNoRegistrado').is(':checked')) {
                //console.log('Producto a agregar no existe');
                valDetalleProductosIn.producto = ''
                valDetalleProductosIn.producto_id = 0
                $('#inputValueNewProduct').val('')
            } else {
                console.log('producto si existe');
            }

        });

        $(document).ready(function() {
            // Inicializar Select2 al cargar la página
            $('#producto_select').select2({
                dropdownParent: $("#modalAddProd")
            });

            //$('#productoSelect').select2();
            // Se ejecuta cuando se muestra el modal
            $('#myModal').on('shown.bs.modal', function() {
                initializeSelect2();
            });

            $('#modalAddProd').on('shown.bs.modal', function() {
                initializeSelect2();
            });

            // Desde Livewire, cerrar el modal
            Livewire.on('cerrar-modal', function() {
                cerrarModal();

            });

            $('#producto_select').on('change', function(e) {
                /* console.log('cambio en select');
                console.log(e.target.options[e.target.selectedIndex].text); */
                var productoId = e.target.value;
                var productoText = e.target.options[e.target.selectedIndex].text;

                valDetalleProductosEx.producto_id = productoId
                valDetalleProductosEx.producto = productoText
                /* @this.set('requisicion.producto.producto_id', $(this).val());
                @this.set('requisicion.producto.producto', $(this).find('option:selected').text()); */
                //@this.call('valueSelectProductChange', {producto_id: productoId, producto: productoText});
                /*  @this.set('requisicion.producto.producto_id', $(this).val());
                 @this.set('requisicion.producto.producto', $(this).find('option:selected').text()); */


            });


            $('#proyectoSelect').on('change', function() {

                @this.set('requisicion.proyecto', $(this).find('option:selected').text().trim());


            });

            $('#inputValueNewProduct').on('keyup', function(e) {
                valDetalleProductosIn.producto = e.target.value
            })


            $('#productoNoRegistrado').change(function(e) {
                /* console.log(e.target); */
                if ($('#productoNoRegistrado').is(':checked')) {
                    /* console.log('Producto a agregar no existe'); */
                    valDetalleProductosIn.producto = ''
                    valDetalleProductosIn.producto_id = 0
                    $('#inputValueNewProduct').val('')
                } else {
                    console.log('producto si existe');

                }
                //console.log('producto no registrado');

                /* valDetalleProductos.producto_id = ''
                valDetalleProductos.producto = ''
                $('#inputValueNewProduct').val('')
                $('#productoSelect').val('') */

                // Verifica si el checkbox está seleccionado
                /*if ($(this).is(':checked')) {
                    valDetalleProductos.producto_id = ''
                    valDetalleProductos.producto = ''
                     @this.set('requisicion.producto.producto', "");
                    @this.set('requisicion.producto.producto_id', 0); 
                }*/
            });


        });
    </script>
</div>
