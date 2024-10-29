<div class="p-3">
    <div class="row">
        <div class="col-12 d-flex justify-between mb-3">
            <div class="w-50">
                <input type="text" class="form-control " id="input_search_estatus" placeholder="Buscar...">
            </div>
            <div class="me-5">
                <button class="btn btn-primary" wire:click="$set('modalNuevoEstatus',true)">Agregar Estatus</button>
            </div>
        </div>
    </div>
    <div class="col-12">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Descripcion</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estatus as $item)
                    <tr>
                        <th scope="row">
                            {{ $item->id }}
                        </th>
                        <td> {{ $item->name }} </td>
                        <td> {{ $item->descripcion ?? 'Sin Descripción' }} </td>
                        <td>
                            <button class="btn btn-primary btn-sm">Editar</button>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    <x-dialog-modal wire:model="modalNuevoEstatus">
        <x-slot name="title">
            Nuevo Estatus
        </x-slot>
        <x-slot name="content">
            <div class="mb-2 w-full">
                <x-label for="">Nombre</x-label>
                <input wire:model='nuevo_estatus'
                    class="w-full border-1 border-slate-900 rounded-md  text-black text-md " type="text"
                    id="nuevo_estatus" name="nuevo_estatus" />
            </div>

            <div class="w-full">
                <x-label>Descripcion</x-label>
                <textarea wire:model="descripcion" class="w-full border rounded-lg p-2 mb-2" placeholder="Descripcion..."
                    rows="2"></textarea>
            </div>

        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="crearNuevoEstatus()">Guardar</x-button>
            <x-button wire:click="$set('modalNuevoEstatus',false)">Cancelar</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
