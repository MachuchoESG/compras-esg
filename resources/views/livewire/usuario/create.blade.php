<div class="p-2">

    <div class="flex justify-end">
        <button
            class="hover:pointertext-white hover:bg-blue-400 font-bold text-sm bg-blue-500  inline-flex p-2  items-center rounded-lg text-gray-50"
            wire:click="$set('open', true)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-person-add" viewBox="0 0 16 16">
                <path
                    d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                <path
                    d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z" />
            </svg>

            <span class=" mx-2">Nuevo Usuario</span>
        </button>
    </div>


    <x-dialog-modal wire:model='open'>
        <x-slot name='title'>
            Registro de Usuario
        </x-slot>


        <x-slot name='content'>
            <x-validation-errors class="mb-4" />
            <form wire:submit="save">
                @csrf

                <div>
                    <x-label for="name" value="{{ __('Name') }}" />
                    <x-input wire:model="usuario.name" id="usuario.name" class="block mt-1 w-full" type="text"
                        name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error for="usuario.name" />
                </div>

                <div class="mt-4">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input wire:model="usuario.email" id="usuario.email" class="block mt-1 w-full" type="email"
                        name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error for="usuario.email" />
                </div>

                <div class="mt-4">
                    <x-label for="password" value="{{ __('Password') }}" />
                    <x-input wire:model="usuario.password" id="password" class="block mt-1 w-full" type="password"
                        name="password" required autocomplete="new-password" />
                </div>
                <div class="mt-4">
                    <x-label for="">Departamento</x-label>
                    <select wire:model="usuario.departamento_id" wire:change="obtenerPuestos($event.target.value)"
                        class="border-1 border-slate-900 rounded-md w-full text-black text-md capitalize">
                        <option value="0" disabled>
                            Seleccione un departamento
                        </option>

                        @foreach ($departamentos as $departamento)
                            <option value="{{ $departamento['id'] }}">
                                {{ $departamento->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error for="usuario.departamento_id" />
                </div>

                <div class="mt-4">
                    <x-label for="">Puesto</x-label>
                    <select wire:model="usuario.puesto_id"
                        class="border-1 border-slate-900 rounded-md w-full text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione un puesto
                        </option>

                        @foreach ($puestos as $puesto)
                            <option value="{{ $puesto['id'] }}">
                                {{ $puesto->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error for="usuario.puesto_id" />
                </div>

        </x-slot>

        <x-slot name='footer'>
            <x-button class="bg-blue-500 hover:pointer text-white font-bold" type="submit">Crear Usuario</x-button>
            </form>

            <x-button class="bg-blue-500 hover:pointer text-white font-bold"
                wire:click="$set('open', false)">Cancelar</x-button>
        </x-slot>

    </x-dialog-modal>
</div>
