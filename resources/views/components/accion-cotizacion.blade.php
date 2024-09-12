<div class="relative">
    <x-dropdown align="right" width="60">
        <x-slot name="trigger">
            <span class="inline-flex rounded-md bg-sky-700">
                <button class="px-1 py-1 bg-black  transition-colors duration-200 rounded-lg  hover:bg-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                        <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                    </svg>
                </button>
            </span>
        </x-slot>

        <x-slot name="content">
            <div class="w-60">

                <div class="block px-4 py-2 text-xs bg-gray-400 text-gray-100">
                    {{ __('Acciones') }}
                </div>

                <x-dropdown-link id="#alta-producto" class="no-underline text-xs cursor-pointer">
                    Alta Producto
                </x-dropdown-link>



            </div>
        </x-slot>
    </x-dropdown>
    <script>
        $(document).ready(function() {
            $("#alta-producto").on('click', function() {
                Livewire.dispatch('AbrirModal');
            })

        });
    </script>

</div>