<div>
    <livewire:requisicion.component.detallerequisicion :requisicionid="$requisicion->id" />



    <div class="my-4 mx-2 flex justify-around">

        @can('delete',$requisicion)
        <x-button wire:click="delete" class="bg-red-500 hover:bg-red-400 active:bg-red-300 focus:bg-red-400">Eliminar</x-button>

        <x-button-nav class="bg-blue-500 hover:bg-blue-400 active:bg-blue-300 focus:bg-blue-400" :href="route('requisicion.edit', ['requisicion' => $requisicion->id]) ">Editar</x-button-nav>

        @endcan

    </div>


  

        
    <x-dialog-modal wire:model="openIncompleta">
        <x-slot name="title">
           Requisicion Incompleta
        </x-slot>
        <x-slot name="content">

           
            <div class="w-full">
                <x-label>Comentario</x-label>
                <textarea wire:model="comentario" class="w-full border rounded-lg p-2 mb-2" placeholder="Comentario..." rows="4"></textarea>
                <x-input-error for="comentario" />
            </div>
          
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="incompleta()">Guardar</x-button>
            <x-button wire:click="$set('openIncompleta',false)">Cancelar</x-button>
        </x-slot>
    </x-dialog-modal>
    
</div>