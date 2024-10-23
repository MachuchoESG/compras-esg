<div class="p-2 ">
    <div class="flex justify-between py-2 mb-2">

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

        <x-button-nav class="bg-black" :href="route('requisicion.create')">Nueva Requisicion</x-button-nav>
    </div>



    <div class="mb-4 w-1/2 flex items-center">
        <label class="text-gray-700 text-sm font-bold mr-2" for="paginate">
            Mostrar
        </label>
        <select wire:model.live="paginate" id="paginate"
            class="w-32 appearance-none bg-white border border-gray-300 hover:border-gray-500 px-4 py-2 rounded shadow leading-tight focus:outline-none focus:shadow-outline-blue focus:border-blue-300">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
        </select>
    </div>


    <div class="w-full p-2 overflow-x-auto">
        <table class="w-full shadow-lg">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-2 py-2">Folio</th>
                    <th class="px-2 py-2">Estatus</th>
                    <th class="px-2 py-2">Orden Compra</th>
                    <th class="px-2 py-2">Fecha Requerida</th>
                    <th class="px-2 py-2">Observaciones</th>
                    <th class="px-2 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requisiciones as $requisicion)
                    <tr class="align-items-center">
                        <td class="px-2 py-2 sm:px-1 sm:py-1">
                            <div>

                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Solicitante
                                        {{ $requisicion->solicitante->name }}</span>
                                    <span class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($requisicion->created_at)->format('d/m/Y') }}</span>
                                </div>

                                <p> {{ $requisicion->folio }}</p>


                            </div>
                        </td>
                        <td class="px-2 py-2 sm:px-1 sm:py-1 ">


                            <div class="flex flex-col">
                                <span>{{ $requisicion->estatus->name }}</span>
                                <p>


                                    @if ($this->estatus($requisicion))
                                        <span
                                            class="bg-emerald-200 text-emerald-700 text-sm font-bold me-2 px-2.5 py-0.5 rounded">{{ $this->estatus($requisicion) }}</span>
                                    @endif
                                </p>
                            </div>


                        </td>
                        <td class="px-2 py-2 sm:px-1 sm:py-1 text-center">
                            <div class="flex flex-col justify-center items-center">
                                <span class="text-sm text-gray-500">{{ $requisicion->proveedor }}</span>
                                <span>{{ $requisicion->ordenCompra }}</span>
                            </div>
                        </td>

                        <td class="px-2 py-2 sm:px-1 sm:py-1">
                            {{ \Carbon\Carbon::parse($requisicion->fecharequerida)->format('d/m/Y') }}</td>
                        <td class="px-2 py-2 sm:px-1 ">{{ $requisicion->observaciones }}</td>
                        <td class="px-2 py-2 sm:px-1 sm:py-1">

                            @livewire('component.menurequisicion', ['requisicion' => $requisicion], key('menurequisicion_' . $requisicion->id))

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="modaldelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        wire:ignore>
        <div class="modal-dialog">
            <div class="modal-content" id="contenido-modal-borrar">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Borrar Requisicion: <span
                            id="folio-requisicion">Cargando...</span></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><span class="fw-bold">Requisicion:</span> <span id="folio-requisicion-body">Cargando...</span>
                    </p>

                    <div class="p-3 text-warning-emphasis bg-warning-subtle border border-warning-subtle rounded-3">
                        Al dar clic en el botón "BORRAR" la requisición seleccionada se eliminara del listado de todas
                        las requisiciones.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btn-borrar-requisicion"
                        wire:click="borrarRequisicion()" disabled>BORRAR</button>
                </div>
            </div>
        </div>
    </div>



    <div class="mt-2 mb-2">
        {{ $requisiciones->links() }}
    </div>

    <script>
        window.addEventListener('cerrar-modal-borrar', event => {
            $(`#modaldelete`).modal('hide');
            window.location.reload();
            $('#folio-requisicion').text('Cargando...');
            $('#folio-requisicion-body').text('Cargando...');
            $('#btn-borrar-requisicion').prop("disabled", true);
            //@this.call('renderRequisiciones');
        });

        window.addEventListener('selected-requisicion-borrar', event => {
            var dataj = event.detail[0].requisicion; // TRAE LOS DATOS DESDE COMPONENTE MENUREQUISICION
            $('#folio-requisicion').text('Cargando...');
            $('#folio-requisicion-body').text('Cargando...');
            @this.call('setValueBorrar', dataj);

        });

        window.addEventListener('abrir-modal-borrar', event => {
            console.log('show');
            var dataj = event.detail[0].requisicionborrar;
            console.log(dataj);
            $('#folio-requisicion').text(dataj.folio);
            $('#folio-requisicion-body').text(dataj.folio);
            $('#btn-borrar-requisicion').prop("disabled", false);

            //$('#btn-borrar-requisicion').attr('data-id', dataj.id);
            $(`#modaldelete`).modal('show');
            //@this.call('renderRequisiciones');

            $('#modaldelete').on('hidden.bs.modal', function(e) {
                // Aquí puedes agregar el código que quieres ejecutar cuando el modal se cierre
                $('#btn-borrar-requisicion').prop("disabled", true);
                $('#folio-requisicion').text('Cargando...');
                $('#folio-requisicion-body').text('Cargando...');
            });
        });
    </script>

</div>
