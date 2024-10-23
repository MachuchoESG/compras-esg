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
                    @if ($requisicion->estatus_id !== 6)
                        <x-dropdown-link class="no-underline text-xs" :href="route('cotizacion.show', ['cotizacion' => $requisicion->id])">
                            {{ __('Agregar cotizacion') }}
                        </x-dropdown-link>
                    @endif

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
                        wire:click="setRequisicionBorrar({{ $requisicion->id }})" data-bs-target="#modaldelete">
                        {{ __('Borrar') }} - {{ $requisicion->folio }}
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


    <script>
        $('#folio-requisicion').text('Cargando...');
        $('#folio-requisicion-body').text('Cargando...');
    </script>
</div>
