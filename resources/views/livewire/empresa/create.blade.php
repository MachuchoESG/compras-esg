<div>
    <x-button wire:click="$set('open',true)">Nueva Empresa</x-button>

    <x-dialog-modal wire:model='open'>
        <x-slot name='title'>
            Registro de empresa
        </x-slot>

        <x-slot name='content'>
            <form wire:submit="save">
                @csrf

                <div>

                    <x-label for="nombre">Nombre de la Empresa:</x-label>
                    <input class="w-full" wire:model="nombre" type="text" id="nombre" name="nombre" required>



                </div>


        </x-slot>

        <x-slot name='footer'>
            <x-button class="bg-blue-500 hover:pointer text-white font-bold" type="submit">Crear Empresa</x-button>
            </form>

            <x-button class="bg-blue-500 hover:pointer text-white font-bold" wire:click="$set('open', false)">Cancelar</x-button>
        </x-slot>

    </x-dialog-modal>
</div>