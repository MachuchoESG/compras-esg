<div x-data="{ open: @entangle($attributes->wire('model')) }">
    <div x-show="open" class="fixed inset-0 overflow-y-auto px-4 py-6 z-50">
        <div class="flex items-center justify-center min-h-screen">
            <!-- Fondo oscuro transparente -->
            <div x-show="open" class="fixed inset-0 transition-opacity" x-on:click="open = false">
                <div class="absolute inset-0 bg-black opacity-50" @click.stop=""></div>
            </div>

            <!-- Contenedor del modal -->
            <div x-show="open" class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full md:max-w-2xl">
                <!-- Header -->
                <div class="bg-gray-100 px-4 py-3">
                    <h3 class="text-lg font-bold text-gray-800">{{ $title }}</h3>
                </div>

                <!-- Content -->
                <div class="px-4 py-6">
                    {{ $content }}
                </div>

                <!-- Footer -->
                <div class="bg-gray-100 px-4 py-3 flex justify-end">
                    {{ $footer }}
                </div>
            </div>
        </div>
    </div>
</div>
