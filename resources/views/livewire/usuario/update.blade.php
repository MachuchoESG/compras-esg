<div>
    <x-dialog-modal id="modal-update-user" wire:model='openMUpdateUser'>
        <x-slot name='title'>
            Editar datos Usuario
        </x-slot>
        <form {{-- wire:submit="save" --}}>
            @csrf
            <x-slot name='content'>

                <div class="mb-3">
                    <x-label for="name">Nombre de Usuario:</x-label>
                    <input class="w-full" wire:model="name" type="text" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <x-label for="email">Email:</x-label>
                    <input class="w-full" wire:model="email" type="text" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <x-label for="">Departamento</x-label>
                    <select wire:model="departamento_id" wire:change="obtenerPuestos($event.target.value)"
                        class="border-1 border-slate-900 rounded-md w-full text-black text-md capitalize">
                        <option value="0" disabled>
                            Seleccione un departamento
                        </option>

                        @foreach ($lista_departamentos as $departamento)
                            <option value="{{ $departamento['id'] }}"
                                {{ $departamento['id'] === $userSelected['departamento_id'] && 'selected' }}>
                                {{ $departamento->name }} </option>
                        @endforeach
                    </select>
                    <x-input-error for="departamento_id" />
                </div>

                <div class="mb-3">
                    <x-label for="put-puesto">Puesto</x-label>
                    <select wire:model="puesto_id" id="put-puesto"
                        class="border-1 border-slate-900 rounded-md w-full text-black text-md capitalize">
                        <option value="0" disabled>
                            Seleccione un puesto
                        </option>

                        @foreach ($lista_puestos as $lp)
                            <option value="{{ $lp['id'] }}"
                                {{ $lp['id'] === $userSelected['puesto_id'] && 'selected' }}>
                                {{ $lp->name }}
                            </option>
                        @endforeach

                    </select>
                    <x-input-error for="puesto_id" />
                </div>

            </x-slot>

            <x-slot name='footer'>
                <x-button class="bg-blue-500 hover:pointer text-white font-bold" wire:click="save">Guardar</x-button>


                <x-button class="bg-blue-500 hover:pointer text-white font-bold"
                    wire:click="cerrarModal">Cancelar</x-button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>
