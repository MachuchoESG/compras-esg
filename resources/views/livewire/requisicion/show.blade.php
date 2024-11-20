<div>
    @if($requisicion)
        <livewire:requisicion.component.detallerequisicion :id="$requisicion->id" />
    @endif
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