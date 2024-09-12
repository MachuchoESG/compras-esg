<div class="p-2">

    <div class="flex justify-end">
        <button class="hover:pointertext-white hover:bg-blue-400 font-bold text-sm bg-blue-500  inline-flex p-2  items-center rounded-lg text-gray-50" wire:click="$set('open', true)">

            <span class=" mx-2">{{__('Nuevo Departamento')}}</span>
        </button>
    </div>



    <x-dialog-modal wire:model='open'>
        <x-slot name='title'>
            Registro de departamento
        </x-slot>

        <x-slot name='content'>
            <form wire:submit="save">
                @csrf

                <div>

                    <x-label for="nombre">Nombre del Departamento:</x-label>
                    <input class="w-full" wire:model="nombre" type="text" id="nombre" name="nombre" required>



                </div>


        </x-slot>

        <x-slot name='footer'>
            <x-button class="bg-blue-500 hover:pointer text-white font-bold" type="submit">Crear Departamento</x-button>
            </form>

            <x-button class="bg-blue-500 hover:pointer text-white font-bold" wire:click="$set('open', false)">Cancelar</x-button>
        </x-slot>

    </x-dialog-modal>
</div>