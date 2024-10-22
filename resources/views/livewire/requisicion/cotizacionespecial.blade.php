<div class="container pt-2">
    <livewire:requisicion.component.informacion-requisicion :requisicion="$requisicion" />
    <div class="row mb-3">
        <div class="col-12">
            Agregue la informacion del producto/servicio para generar la cotizacion en base a los datos recopilados.
        </div>
        <div class="col-12 col-md-6">
            <p class="mb-2 fw-bold">Productos - Cantidad</p>
            <ul class="list-group">
                @foreach ($productosRequisicion as $item)
                    <li class="list-group-item"> {{ $item->producto }} - {{ $item->cantidad }} </li>
                @endforeach
            </ul>
        </div>
        <div class="col-12 col-md-6">
            <label for="" class="form-label fw-bold">Observacion Especial</label>
            <textarea class="form-control" id="exampleFormControlTextarea1" rows="4" wire:model='observacionEspecial'></textarea>
        </div>
    </div>
    <div class="d-flex justify-content-center mb-3">
        @if ($requisicion->estatus_id == 13)
            <x-button wire:click="continuarRequisicion()">Continuar requisición</x-button>
        @endif

    </div>

</div>
