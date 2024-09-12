<div>
    <div class=" text-justify relative">
        <x-dropdown align="right" width="60">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button class="px-1 py-1 text-black transition-colors duration-200 rounded-lg dark:text-gray-300 hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                <div class="w-60">

                    <div class="block px-4 py-2 text-xs bg-gray-400 text-gray-100">
                        {{ __('Acciones') }}
                    </div>

                    <x-dropdown-link id="alta-producto" class="no-underline text-xs cursor-pointer">
                        <div>
                            Alta Producto
                        </div>
                    </x-dropdown-link>
                    <x-dropdown-link id="editar-producto" class="no-underline text-xs cursor-pointer">
                        <div>
                            Asignar producto
                        </div>
                    </x-dropdown-link>



                </div>
            </x-slot>
        </x-dropdown>


        <script>
            $(document).ready(function() {
                $("#alta-producto").on('click', function() {
                    Livewire.dispatch('AbrirModalProducto');
                })

                $("#editar-producto").on('click', function() {
                    Livewire.dispatch('AbrirModalEditarDetalle', ['{{ $detalle->id }}']);
                })



            });
        </script>

    </div>
</div>