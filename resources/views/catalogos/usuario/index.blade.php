<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Usuarios') }}
        </h2>
    </x-slot>

    <div class="pt-3 pt-md-5">
        <div class="">
            <div class="bg-white mx-2 px-2 overflow-hidden shadow-xl sm:rounded-lg">
                @livewire('usuario.index')
            </div>
        </div>
    </div>
</x-app-layout>
