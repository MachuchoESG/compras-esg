<div class="p-4 border rounded mb-4">
    <div class="w-full md:flex md:justify-between md:items-center">
        <div class="flex flex-col">
            <span class="text-sm text-gray-500"> Fecha Creacion
                {{ \Carbon\Carbon::parse($requisicion->created_at)->locale('es')->isoFormat('LL') }}</span>
            <p class="text-gray-500 text-md font-bold m-0">Folio: {{ $requisicion->folio }}</p>
            <p class="text-gray-500 text-md font-bold">Estatus: {{ $requisicion->estatus->name }}</p>
        </div>
        @if ($requisicion->evidencia()->exists())
            <button wire:click.prevent="download({{ $requisicion->evidencia()->first()->id }})"
                class=" relative inline-flex items-center  px-1 py-2 text-sm font-medium rounded-lgfocus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 ">
                <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path
                        d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z" />
                    <path
                        d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                </svg>
                Evidencia
            </button>
        @endif
    </div>


    <div class="md:flex md:justify-between ">
        <p class="text-gray-500 text-md">Solicitante: {{ $requisicion->solicitante->getNombreCompleto() }}</p>
        <p class="text-gray-500 text-md">
            Fecha Requerida: {{ \Carbon\Carbon::parse($requisicion->fecharequerida)->locale('es')->isoFormat('LL') }}
            ({{ \Carbon\Carbon::parse($requisicion->fecharequerida)->locale('es')->diffForHumans() }})
        </p>

    </div>

    <p class="text-gray-500 text-md">Observaciones: {{ $requisicion->observaciones }}</p>

    @if (!empty($requisicion->unidad))
        <p class="text-gray-500 text-md">Unidad de Taller: {{ $requisicion->unidad }}</p>
    @endif
    <div class="d-flex justify-content-between">
        <p class="text-gray-500 text-md">Cotización Unica: {{ $requisicion->cotizacion_unica ? 'Si' : 'No' }}</p>

        @if ($requisicion->cotizacion_especial)
            <p class="text-gray-500 text-md">Cotización Especial: {{ $requisicion->cotizacion_especial ? 'Si' : 'No' }}
            </p>
            <p class="text-gray-500 text-md">Cotización Departamento Asignado:
                {{ $departamentoAsignado->name ?? 'NO APLICA' }}
            </p>
        @endif

    </div>
    @if ($requisicion->cotizacion_especial)
        <div class="">
            <p class="fw-bold m-0">Observacion Especial:</p>
            <p>{{ $requisicion->observacion_especial ?? 'NO CONTIENE OBSERVACION' }}</p>
        </div>
    @endif


    @if ($requisicion->comentarios->count() > 0)
        <x-button wire:click="$set('openComentarios', true)" class="my-2">Ver Comentarios</x-button>
    @endif

    <livewire:requisicion.component.comentarios :requisicion="$requisicion" wire:model="openComentarios" />

</div>
