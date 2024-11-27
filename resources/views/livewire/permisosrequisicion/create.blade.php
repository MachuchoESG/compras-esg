<div class="">
    <div class="flex justify-end">
        <button class="hover:pointertext-white hover:bg-blue-400 font-bold text-sm bg-blue-500  inline-flex p-2  items-center rounded-lg text-gray-50" wire:click="$set('open', true)">
            <span class=" mx-2">Nuevo Flujo</span>
        </button>
    </div>



    <x-dialog-modal wire:model='open'>
        <x-slot name='title'>
            Nuevo flujo de autorizacion
        </x-slot>

        <x-slot name='content'>
            <form wire:submit="save">
                @csrf


                <div class="w-full">
                    <x-label for="">Puesto solicitante</x-label>
                    <select wire:model="permiso.PuestoSolicitante_id" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione un puesto
                        </option>

                        @foreach ($puestos as $puesto)
                        <option value="{{ $puesto['id'] }}">
                            {{ $puesto->name}}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="permiso.PuestoSolicitante_id" />
                </div>

                <div class="w-full">
                    <x-label for="">Puesto Autorizador</x-label>
                    <select wire:model="permiso.PuestoAutorizador_id" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione un puesto
                        </option>

                        @foreach ($puestos as $puesto)
                        <option value="{{ $puesto['id'] }}">
                            {{ $puesto->name}}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="permiso.PuestoAutorizador_id" />
                </div>

                <div class="w-full">
                    <x-label for="">Departamento</x-label>
                    <select wire:model="permiso.Departamento_id" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione un departamento
                        </option>

                        @foreach ($departamentos as $departamento)
                        <option value="{{ $departamento['id'] }}">
                            {{ $departamento->name}}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="permiso.Departamento_id" />
                </div>

                <div>
                    <x-label for="cantidad">Monto Maximo</x-label>
                    <input wire:model="permiso.monto" class="w-full h-10 border rounded-lg mb-2" type="number" id="cantidad" name="cantidad" required>
                    <x-input-error for="permiso.monto" />
                </div>



        </x-slot>

        <x-slot name='footer'>
            <x-button class="bg-blue-500 hover:pointer text-white font-bold" type="submit">Guardar</x-button>
            </form>

            <x-button class="bg-blue-500 hover:pointer text-white font-bold" wire:click="$set('open', false)">Cancelar</x-button>
        </x-slot>

    </x-dialog-modal>
</div>