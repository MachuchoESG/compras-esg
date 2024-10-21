<div class="">
    <x-dropdown align="right" width="60">
        <x-slot name="trigger">
            <span class="inline-flex rounded-md">
                <button
                    class="px-1 py-1 text-gray-500 transition-colors duration-200 rounded-lg dark:text-gray-300 hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                    </svg>
                </button>
            </span>
        </x-slot>

        <x-slot name="content">
            <div class="w-60">

                <div class="block px-4 py-2 text-xs bg-gray-400 text-gray-100">
                    {{ __('Acciones') }}
                </div>

                @if ($requisicion->aprobado && $escompras)
                    <x-dropdown-link class="no-underline text-xs" :href="route('cotizacion.show', ['cotizacion' => $requisicion->id])">
                        {{ __('Agregar cotizacion') }}
                    </x-dropdown-link>
                @endif

                @if ($requisicion->aprobado && $requisicion->estatus_id != 1 && $requisicion->estatus_id != 6)
                    @can('autorizar', $requisicion)
                        <x-dropdown-link class="no-underline text-xs" :href="route('requisicion.autorizar', ['requisicion' => $requisicion->id])">
                            {{ __('Autorizar') }}
                        </x-dropdown-link>
                    @endcan
                @endif



                @if (!$requisicion->aprobado && $esjefe)
                    @can('aprobar', $requisicion)
                        <x-dropdown-link class="no-underline text-xs" :href="route('requisicion.aprobacion', ['requisicion' => $requisicion])">
                            {{ __('Aprobar Requisicion') }}
                        </x-dropdown-link>
                    @endcan
                @endif



                @foreach ($requisicion->evidencia as $archivo)
                    <x-dropdown-link class="no-underline text-xs cursor-pointer"
                        wire:click.p%revent="download({{ $archivo->id }})">
                        {{ __('Evidencia') }}
                    </x-dropdown-link>
                @endforeach


                <x-dropdown-link class="no-underline text-xs cursor-pointer" :href="route('requisicion.show', ['requisicion' => $requisicion->id])">
                    {{ __('Ver') }}
                </x-dropdown-link>

                @if (Auth::id() == 30 || Auth::id() == 33)
                    {{-- lm && yar --}}
                    <x-dropdown-link class="no-underline text-xs cursor-pointer" data-bs-toggle="modal"
                        data-bs-target="#modaldelete">
                        {{ __('Borrar') }}
                    </x-dropdown-link>
                @endif
                @if ($requisicion->cotizacion_especial == 1 && $requisicion->aprobado == 1 && $requisicion->estatus_id == 13)
                    <x-dropdown-link class="no-underline text-xs cursor-pointer" :href="route('requisicion.cotizacionespecial', ['requisicion' => $requisicion->id])">
                        {{ __('Cotizar Especial') }}
                    </x-dropdown-link>
                @endif


            </div>
        </x-slot>
    </x-dropdown>
    <div class="modal fade" id="modaldelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Borrar Requisicion: {{ $requisicion->folio }}
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><span class="fw-bold">Requisicion:</span> {{ $requisicion->folio }}</p>
                    <p><span class="fw-bold">Estatus:</span> {{ $requisicion->estatus->name }} </p>
                    <p><span class="fw-bold">Solicitante:</span> {{ $requisicion->solicitante->name }}</p>

                    <div class="p-3 text-warning-emphasis bg-warning-subtle border border-warning-subtle rounded-3">
                        Al dar clic en el botón "BORRAR" la requisición seleccionada se eliminara del listado de todas
                        las requisiciones.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="borrarRequisicion()">BORRAR</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('cerrar-modal-borrar', event => {
            $(`#modaldelete`).modal('hide');
            window.location.reload();
            //@this.call('renderRequisiciones');
        });
    </script>
</div>
