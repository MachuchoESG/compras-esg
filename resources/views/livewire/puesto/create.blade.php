<div class="p-2">

    <div class="flex justify-end">
        <button class="hover:pointertext-white hover:bg-blue-400 font-bold text-sm bg-blue-500  inline-flex p-2  items-center rounded-lg text-gray-50" wire:click="$set('open', true)">
            <span class=" mx-2">Nuevo Puesto</span>
        </button>
    </div>


    <x-dialog-modal wire:model='open'>
        <x-slot name='title'>
            Registro de puestos
        </x-slot>

        <x-slot name='content'>
            <form wire:submit="save">
                @csrf

                <div>
                    <x-label for="nombre">Nombre del Puesto:</x-label>
                    <input class="w-full" wire:model="nombre" type="text" id="nombre" name="nombre" required>
                    <x-input-error for="nombre" />
                </div>

                <div class="w-full">
                    <x-label for="">Departamento</x-label>
                    <select wire:model="departamento_id" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione un departamento
                        </option>

                        @foreach ($departamentos as $departamento)
                        <option value="{{ $departamento['id'] }}">
                            {{ $departamento->name}}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="departamento_id" />
                </div>


        </x-slot>

        <x-slot name='footer'>
            <x-button class="bg-blue-500 hover:pointer text-white font-bold" type="submit">Crear Puesto</x-button>
            </form>

            <x-button class="bg-blue-500 hover:pointer text-white font-bold" wire:click="$set('open', false)">Cancelar</x-button>
        </x-slot>

    </x-dialog-modal>
</div>