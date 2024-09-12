<div>

    <x-button class="bg-blue-500 hover:pointer text-white font-bold" wire:click="$set('open', true)">Nuevo
        Proveedor</x-button>

    <x-dialog-modal wire:model='open'>
        <x-slot name='title'>
            Registro de proveedor
        </x-slot>

        <x-slot name='content'>
            <form wire:submit="save">
                @csrf

                <div class="w-full">
                    <x-label for="">Sucursal</x-label>
                    <select wire:model.live="sucursal_id"
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
                    <x-input-error for="sucursal_id" />
                </div>
                <div>
                    <x-label for="nombre">Razon Social:</x-label>
                    <x-input class="w-full" wire:model="proveedor.crazonsocial" type="text" id="nombre"
                        name="nombre" />
                    <x-input-error for="proveedor.crazonsocial" />
                </div>

                <div>
                    <x-label for="nombre">RFC</x-label>
                    <x-input class="w-full" wire:model="proveedor.crfc" type="text" id="nombre" name="nombre" />
                    <x-input-error for="proveedor.crfc" />
                </div>
                <div>
                    <x-label for="nombre">Correo electronico</x-label>
                    <x-input class="w-full" wire:model="proveedor.cemail1" type="email" id="nombre"
                        name="nombre" />
                    <x-input-error for="proveedor.cemail1" />
                </div>




        </x-slot>

        <x-slot name='footer'>
            <x-button class="bg-blue-500 hover:pointer text-white font-bold" type="submit">Guardar</x-button>
            </form>

            <x-button class="bg-blue-500 hover:pointer text-white font-bold"
                wire:click="$set('open', false)">Cancelar</x-button>
        </x-slot>

    </x-dialog-modal>
</div>
