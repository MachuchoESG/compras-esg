<div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
        Agregar Cotizacion
    </button>

    <div wire:ignore.self class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detalle Productos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">


                    <div>
                        <x-label for="">Producto</x-label>
                        <select wire:model.live="producto.id_Producto" class="select2 w-full" id="productoSelect">

                            <option value="" selected disabled>Seleccionar producto</option>


                            @foreach ($productos as $producto)
                            <option value="{{$producto['cidproducto']}}">{{$producto['cnombreproducto']}}</option>
                            @endforeach

                        </select>
                        <x-input-error for="producto.id_Producto" />

                    </div>



                    <div>
                        <x-label for="cantidad">Cantidad</x-label>
                        <input wire:model="producto.cantidad" class="w-full h-10 border rounded-lg mb-2" type="number" id="cantidad" name="cantidad">
                        <x-input-error for="producto.cantidad" />
                    </div>

                    <div>
                        <x-label for="observaciones">Observaciones</x-label>
                        <textarea wire:model="producto.observaciones" class="w-full border rounded-lg p-2 mb-2" placeholder="Observaciones..." id="observaciones" name="observaciones" rows="4"></textarea>
                        <x-input-error for="producto.observaciones" />
                    </div>



                    <div>

                        <x-label for="existencias">Total de existencias</x-label>
                        <input wire:model.live="existencias" class="w-full h-10 border rounded-lg mb-2" type="number" id="existencias" name="existencias">

                    </div>


                </div>
                <div class="modal-footer">
                    <x-button wire:click="guardarProducto" class="ml-2 bg-blue-500 hover:bg-blue-700">Guardar</x-button>
                </div>

            </div>
        </div>



    </div>

    <script>
        function initializeSelect2() {
            $('.select2').select2({
                dropdownParent: $("#myModal")
            });
        }



        $(document).ready(function() {
            // Inicializar Select2 al cargar la página
            initializeSelect2();

            // Se ejecuta cuando se muestra el modal
            $('#myModal').on('shown.bs.modal', function() {
                initializeSelect2();
            });


            $('#productoSelect').on('change', function() {
                @this.set('producto.id_Producto', $(this).val());
                @this.set('producto.producto', $(this).find('option:selected').text());


            });




        });
    </script>
</div>