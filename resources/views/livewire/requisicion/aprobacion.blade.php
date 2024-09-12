<div class="p-2">

    <livewire:requisicion.component.informacion-requisicion :requisicion="$requisicion" />

    @if ($requisicion->detalleRequisiciones->isNotEmpty())
    <div class="w-full">
        <table class="w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border-b">Cantidad</th>
                    <th class="py-2 px-4 border-b">Producto</th>
                    <th class="py-2 px-4 border-b">Observaciones</th>

                    <!-- Agrega más columnas según tus necesidades -->
                </tr>
            </thead>
            <tbody>

                @foreach ($requisicion->detalleRequisiciones as $detalle)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $detalle->cantidad }}</td>
                    <td class="py-2 px-4 border-b">{{ $detalle->producto }}</td>
                    <td class="py-2 px-4 border-b">{{ $detalle->observaciones }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="w-full h-16 flex justify-around items-center">
            <x-button class=" bg-blue-600" wire:click="aprobar">Aprobar</x-button>

            <x-button class="bg-red-500" wire:click="noaprobar">No Aprobar</x-button>
        </div>
    </div>
    @else
    <p class="text-gray-600">No hay detalles disponibles para esta requisición.</p>
    @endif
</div>