<div class="ms-3 relative">
    <x-dropdown align="right" width="60">
        <x-slot name="trigger">
            <span class="inline-flex rounded-md">
                <button type="button"
                    class="inline-flex items-center relative px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-bell-fill" viewBox="0 0 16 16">
                        <path
                            d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901" />
                    </svg>

                    @if ($totalnotificaciones != 0)
                        <span
                            class="absolute top-0 right-0 bg-red-500 text-white w-5 h-5 flex justify-center items-center rounded-full text-xs">{{ $totalnotificaciones }}</span>
                    @endif

                </button>

            </span>
        </x-slot>

        <x-slot name="content">
            <div class="w-60">

                @if ($escompras)
                    <div class="block px-4 py-2 text-xs text-gray-400" style="overflow-y: auto; max-height: 40vh">
                        {{ __('Requisiciones') }}


                        @if ($cantidadPendienteCotizacion > 0)
                            <p>Pendientes de subir cotizacion</p>
                            @foreach ($pendientecotizacion as $requisicion)
                                <x-dropdown-link
                                    href="{{ route('cotizacion.show', ['cotizacion' => $requisicion->id]) }}">
                                    <p>{{ $requisicion->folio }}</p>
                                    <span clas>{{ $requisicion->solicitante->name }}</span>
                                    <span>({{ \Carbon\Carbon::parse($requisicion->fecharequerida)->locale('es')->diffForHumans() }})</span>

                                </x-dropdown-link>
                            @endforeach
                        @else
                            <p>No hay requisiciones pendientes de subir cotizaciones.</p>
                        @endif



                    </div>
                @endif

                @if ($esjefe)
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Requisiciones') }}


                        @if ($cantidadPendienteAprobar > 0)
                            <p>Pendientes de aprobar</p>
                            @foreach ($pendientesaprobar as $requisicion)
                                <x-dropdown-link
                                    href="{{ route('requisicion.aprobacion', ['requisicion' => $requisicion]) }}">
                                    {{ $requisicion->folio }}
                                </x-dropdown-link>
                            @endforeach
                        @else
                            <p>No hay requisiciones pendientes de aprobar.</p>
                        @endif

                        @if ($cantidadPendienteAutorizar > 0)
                            <p>Pendientes de autorizar</p>
                            @foreach ($pendienteautorizar as $requisicion)
                                <x-dropdown-link
                                    href="{{ route('requisicion.autorizar', ['requisicion' => $requisicion]) }}">
                                    {{ $requisicion->folio }}
                                </x-dropdown-link>
                            @endforeach
                        @else
                            <p>No hay requisiciones pendientes de autorizar.</p>
                        @endif

                    </div>
                @endif


                @if ($cantidadPendienteAutorizarCotizacion > 0)
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Cotizaciones') }}



                        <p>Pendientes Autorizar </p>
                        @foreach ($pendienteAutorizarCotizacion as $requisicion)
                            <x-dropdown-link href="{{ route('cotizacion.show', ['cotizacion' => $requisicion->id]) }}">
                                {{ $requisicion->folio }}
                            </x-dropdown-link>
                        @endforeach




                    </div>
                @endif


                @if ($cantidadPendienteIncompletas > 0)
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Requisiciones') }}



                        <p>Incompletas</p>
                        @foreach ($pendienteIncompletas as $requisicion)
                            <x-dropdown-link
                                href="{{ route('requisicion.edit', ['requisicion' => $requisicion->id]) }}">
                                {{ $requisicion->folio }}
                            </x-dropdown-link>
                        @endforeach




                    </div>
                @endif


            </div>
        </x-slot>
    </x-dropdown>
</div>
