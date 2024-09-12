<div class="p-2">


    <div class="flex justify-end">
        <button class="hover:pointertext-white hover:bg-blue-400 font-bold text-sm bg-blue-500  inline-flex p-2  items-center rounded-lg text-gray-50" wire:click="$set('open', true)">
            <span class=" mx-2">Nueva Sucursal</span>
        </button>
    </div>


    <x-dialog-modal wire:model='open'>
        <x-slot name='title'>
            Registro de sucursal
        </x-slot>

        <x-slot name='content'>
            <form wire:submit="save">
                @csrf

                <div>

                    <x-label for="nombre">Nombre de la Sucursal:</x-label>
                    <x-input class="w-full" wire:model="nombre" type="text" id="nombre" name="nombre" required />



                </div>
                <div>

                    <x-label for="nombre">Nomenclatura</x-label>
                    <x-input class="w-full" wire:model="nomenclatura" type="text" id="nombre" name="nombre" required />



                </div>
                <div class="w-full">
                    <x-label for="">Empresas</x-label>
                    <select wire:model="empresa_id" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione una empresa
                        </option>

                        @foreach ($empresas as $empresa)
                        <option value="{{ $empresa['id'] }}">
                            {{ $empresa->name}}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="empresa_id" />
                </div>


        </x-slot>

        <x-slot name='footer'>
            <x-button class="bg-blue-500 hover:pointer text-white font-bold" type="submit">Crear Sucursal</x-button>
            </form>

            <x-button class="bg-blue-500 hover:pointer text-white font-bold" wire:click="$set('open', false)">Cancelar</x-button>
        </x-slot>

    </x-dialog-modal>
</div>