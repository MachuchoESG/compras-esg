<div class="p-2">

    <div class="flex justify-between py-2 mb-2">

        <form method="get" class="relative w-1/2">
            <svg width="20" height="20" fill="currentColor" class="absolute left-3 top-1/2 -mt-2.5 text-slate-400 pointer-events-none group-focus-within:text-blue-500" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
            </svg>
            <input type="text" class="w-full focus:ring-2 focus:ring-blue-500 focus:outline-none appearance-none text-sm leading-6 text-slate-900 placeholder-slate-400 rounded-md py-2 pl-10 ring-1 ring-slate-200 shadow-sm" type="text" placeholder="Buscador ..." wire:model.live="search">

        </form>

    </div>

    <div class="relative overflow-x-auto rounded-lg">
        <table class="w-full  text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="text-xs text-gray-700 uppercase bg-gray-200 ">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center">
                        {{__('Id')}}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        {{__('Puesto Solicitante')}}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        {{__('Puesto Autorizador')}}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        {{__('Departamento')}}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        {{__('Monto')}}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        {{__('Action')}}
                    </th>

                </tr>
            </thead>
            <tbody>
                @forelse ($permisos as $permiso)
                <tr class="bg-white border">
                    <td class="px-6 py-3 text-center">{{ $permiso->id }}</td>
                    <td class="px-6 py-3 text-center">{{ $permiso->puestosolicitante->name }}</td>
                    <td class="px-6 py-3 text-center">{{ $permiso->puestoautorizador->name }}</td>
                    <td class="px-6 py-3 text-center">{{ $permiso->departamento->name }}</td>
                    <td class="px-6 py-3 text-center">${{ $permiso->monto }}</td>
                    <td class="px-6 py-3 text-center">
                        <div class="flex  justify-center">
                            <button wire:click=" edit({{$permiso->id}})" class="flex items-center text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                                    <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001" />
                                </svg>
                                <span class="mr-2">Actualizar</span>

                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-3 text-center">Aun no cuentas con flujos de autorizacion.</td>
                </tr>
                @endforelse
            </tbody>
        </table>


    </div>





    <x-dialog-modal wire:model='openEdit'>
        <x-slot name='title'>
            Editar flujo autorización
        </x-slot>

        <x-slot name='content'>
            <form>
                @csrf

                <div class="w-full">
                    <x-label for="">Puesto solicitante</x-label>
                    <select wire:model="permisoEdit.PuestoSolicitante_id" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione un puesto
                        </option>

                        @foreach ($puestos as $puesto)
                        <option value="{{ $puesto['id'] }}">
                            {{ $puesto->name}}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="permisoEdit.PuestoSolicitante_id" />
                </div>

                <div class="w-full">
                    <x-label for="">Puesto Autorizador</x-label>
                    <select wire:model="permisoEdit.PuestoAutorizador_id" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione un puesto
                        </option>

                        @foreach ($puestos as $puesto)
                        <option value="{{ $puesto['id'] }}">
                            {{ $puesto->name}}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="permisoEdit.PuestoAutorizador_id" />
                </div>

                <div class="w-full">
                    <x-label for="">Departamento</x-label>
                    <select wire:model="permisoEdit.Departamento_id" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione un departamento
                        </option>

                        @foreach ($departamentos as $departamento)
                        <option value="{{ $departamento['id'] }}">
                            {{ $departamento->name}}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="permisoEdit.Departamento_id" />
                </div>

                <div>
                    <x-label for="cantidad">Monto Maximo</x-label>
                    <input wire:model="permisoEdit.monto" class="w-full h-10 border rounded-lg mb-2" type="number" id="cantidad" name="cantidad" required>
                    <x-input-error for="permisoEdit.monto" />
                </div>



        </x-slot>

        <x-slot name='footer'>
            <x-button class="bg-blue-500 hover:pointer text-white font-bold" wire:click="update">Actualizar</x-button>
            </form>

            <x-button class="bg-blue-500 hover:pointer text-white font-bold" wire:click="$set('openEdit', false)">Cancelar</x-button>
        </x-slot>

    </x-dialog-modal>


</div>