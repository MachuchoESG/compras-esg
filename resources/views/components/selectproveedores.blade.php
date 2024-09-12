<div>
    <div wire:ignore x-data="{ }" x-init="() => {
	$('.select2').select2();
	$('.select2').on('change', function(e) {
        var razonsocial = $(this).find('option:selected').text();
        @this.set('cotizacion.proveedor', razonsocial);
	let elementNameP = $(this).attr('id');
	@this.set(elementNameP, e.target.value);

		Livewire.hook('message.processed', (m, component) => {
			$('.select2').select2();
		})
	})
}">
        <select class="select2" {{$attributes}} style="width: 100%;">
            <option value="">Seleccione un proveedor</option>
            @foreach ($options as $option)
            <option value="{{$option['cidclienteproveedor']}}">{{$option['crazonsocial']}}</option>
            @endforeach
        </select>
    </div>




</div>