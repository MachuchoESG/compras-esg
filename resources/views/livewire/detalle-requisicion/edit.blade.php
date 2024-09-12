<div>


    <div wire:ignore class="modal fade" id="ModalEditProducto" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalToggleLabel">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="w-full">
                        <label for="">Productos</label>
                        <select class="select2 w-full" name="producto.id_Producto" id="SelectProducto" wire:model.live="producto.id_Producto">
                            <option value="">Selecciona un proveedor</option>
                            @foreach ($listadeproductos as $producto)
                            <option value="{{ $producto['cidproducto'] }}">{{ $producto['cnombreproducto'] }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="producto.id_Producto" />


                    </div>
                </div>
                <div class="modal-footer">

                    <x-button type="submit" class="ml-2" wire:click="update">Guardar</x-button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#SelectProducto').on('change', function() {



                @this.set('producto.id_Producto', $(this).val());
                @this.set('producto.producto', $(this).find('option:selected').text());


            });
        });
    </script>

</div>