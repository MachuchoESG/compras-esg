<div>
    <div wire:ignore x-data="{ }" x-init="() => {
	$('.select2').select2();
	$('.select2').on('change', function(e) {
	let elementName = $(this).attr('id');
	@this.set(elementName, e.target.value);

		Livewire.hook('message.processed', (m, component) => {
			$('.select2').select2();
		})
	})
}">
        <select class="select2" {{$attributes}} style="width: 100%;">
            <option value="">Seleccione un producto</option>
            @foreach ($options as $option)
            <option value="{{$option['cidproducto']}}">{{$option['cnombreproducto']}}</option>
            @endforeach
        </select>
    </div>


    <script>
        $(document).ready(function() {


            // Manejar el cambio en el select
            $('.select2').on('change', function() {


                var producto = $(this).find('option:selected').text(); // Obtiene el nombre del producto seleccionado
                @this.set('producto.producto', producto);

            });





        });
    </script>

</div>