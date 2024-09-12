<div class="p-2">

    <div class="flex justify-between py-2 mb-2">
        @if (!is_null($userSeleccionado) && !empty($userSeleccionado))
            @livewire('usuario.update', ['userSeleccionado' => $userSeleccionado])
        @endif

        <form method="get" class="relative w-1/2">
            <svg width="20" height="20" fill="currentColor"
                class="absolute left-3 top-1/2 -mt-2.5 text-slate-400 pointer-events-none group-focus-within:text-blue-500"
                aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
            </svg>
            <input type="text"
                class="w-full focus:ring-2 focus:ring-blue-500 focus:outline-none appearance-none text-sm leading-6 text-slate-900 placeholder-slate-400 rounded-md py-2 pl-10 ring-1 ring-slate-200 shadow-sm"
                type="text" placeholder="Buscador ..." wire:model.live="search">

        </form>

        @livewire('usuario.create')
    </div>
    <div class="relative overflow-x-auto rounded-lg">
        <table class="w-full  text-sm text-left rtl:text-right text-gray-500 table table-bordered table-hover">
            <thead class="text-xs text-gray-700 uppercase bg-gray-500 ">
                <tr>
                    <th scope="col">
                        {{ __('Name') }}
                    </th>
                    <th scope="col">
                        {{ __('Email') }}
                    </th>
                    <th scope="col">
                        {{ __('Puesto') }}
                    </th>
                    <th scope="col">
                        {{ __('Editar') }}
                    </th>

                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $user)
                    <tr class="bg-white border">
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->puesto->name }}</td>
                        <td>
                            <button class="btn border-t-neutral-600"
                                wire:click="emitirEventOpenUpdateModal({{ $user }})">
                                <x-far-edit class="w-6 h-6" />
                            </button>
                            {{-- <x-button class="bg-blue-500 hover:pointer text-white font-bold"
                                wire:click="emitirEventOpenUpdateModal({{ $user }})">
                                
                            </x-button> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $usuarios->links() }}

    </div>
</div>
<script>
    var targetNode
    // Escuchar el evento modal-closed desde el componente Update
    window.addEventListener('modal-update-closed', event => {
        @this.call('cerrarModal');
    });

    window.addEventListener('modal-update-init', event => {
        console.log('se inicio el modal update');
        targetNode = document.getElementById(
            "modal-update-user");
        const observer = new MutationObserver(callback);

        observer.observe(targetNode, config);

    });

    const config = {
        attributes: true,
    };

    // Callback function to execute when mutations are observed
    const callback = (mutationList, observer) => {
        for (const mutation of mutationList) {
            if (mutation.type === "attributes") {
                console.log(mutation.target
                    ._x_isShown
                ); // a falta de event para detectar modal cerrado, se implementa mutation para ver cambios en modal.
                if (mutation.target._x_isShown === false) {
                    @this.call('cerrarModal');
                }
            }
        }
    };
</script>
