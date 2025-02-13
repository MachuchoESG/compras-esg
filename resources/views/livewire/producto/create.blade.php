<div>


    <!-- <x-button id="btnAddProduct" class="bg-blue-500 hover:pointer text-white font-bold" wire:click="$set('open', true)">Nuevo Producto</x-button> -->

    <x-dialog-modal wire:model='open'>
        <x-slot name='title'>
            Alta producto
        </x-slot>

        <x-slot name='content'>

         

            
            <form wire:submit="save">
                @csrf

                <div class="mb-2 w-full">
                    <x-label for="">Sucursal</x-label>
                    <select wire:model.live="sucursal_id" id="idsucursal" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione una sucursal
                        </option>

                        @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">
                            {{ $sucursal->name }}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="requisicion.sucursal_id" />
                </div>

                <div>
                    <x-label for="nombre">Codigo</x-label>
                    <x-input class="w-full" wire:model="producto.CCODIGOPRODUCTO" type="text" id="nombre" name="nombre" />
                    <x-input-error for="producto.CCODIGOPRODUCTO" />
                </div>
                <div>
                    <x-label for="nombre">Nombre</x-label>
                    <x-input class="w-full" wire:model="producto.CNOMBREPRODUCTO" type="text" id="nombre" name="nombre" />
                    <x-input-error for="producto.CNOMBREPRODUCTO" />
                </div>
                <div class="w-full">
                    <x-label for="">Unidad Medida</x-label>
                    <select wire:model="producto.CIDUNIXML" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione una unidad medida
                        </option>
                        @foreach ($listaunidadmedidas as $unidad)
                        <option value="{{ $unidad['cidunidad'] }}">
                            {{ $unidad['cnombreunidad'] }}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="producto.CIDUNIXML" />
                </div>
                <div class="w-full">
                    <x-label for="">Tipo producto </x-label>
                    <select wire:model="producto.CIDVALORCLASIFICACION1" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione un tipo de producto
                        </option>
                        @foreach ($clasificacion1 as $clasificacion)
                        <option value="{{ $clasificacion['cidvalorclasificacion'] }}">
                            {{ $clasificacion['cvalorclasificacion'] }}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="producto.CIDVALORCLASIFICACION1" />
                </div>
                <div class="w-full">
                    <x-label for="">Consumo Frecuente</x-label>
                    <select wire:model="producto.CIDVALORCLASIFICACION2" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione un tipo de consumo
                        </option>
                        @foreach ($clasificacion2 as $clasificacion)
                        <option value="{{ $clasificacion['cidvalorclasificacion'] }}">
                            {{ $clasificacion['cvalorclasificacion'] }}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="producto.CIDVALORCLASIFICACION2" />
                </div>
               






        </x-slot>

        <x-slot name='footer'>
            <div class="row">
                <div class="col-12 col-sm-4 col-md-4 d-flex justify-content-center">
                    <x-button class="bg-blue-500 hover:pointer text-white font-bold" type="submit">Guardar</x-button>
                </div>
                
            </form>
                <div class="col-12 col-sm-4 col-md-4 d-flex justify-content-center">
                    <x-button class="bg-blue-500 hover:pointer text-white font-bold" wire:click="$set('open', false)">Cancelar</x-button>
                </div>
                <div class="col-12 col-sm-4 col-md-4 d-flex justify-content-center">
                    <x-button class="bg-blue-500 hover:pointer text-white font-bold" wire:click="$set('openConsecutivo', true)">Buscar Consecutivo</x-button>
                </div>
            </div>
           
            
            
        </x-slot>

    </x-dialog-modal>

    <x-dialog-modal  wire:model='openConsecutivo'>
        <x-slot name='title'>
            Buscar consecutivo
        </x-slot>

        <x-slot name='content'>

         

            
            <form wire:submit="buscarconsecutivo">
                @csrf

                <div class="mb-2 w-full">
                    <x-label for="">Sucursal</x-label>
                    <select wire:model.live="sucursal_id" id="idsucursal" class="border-1 border-slate-900 rounded-md w-full  text-black text-md capitalize">
                        <option value="" selected disabled>
                            Seleccione una sucursal
                        </option>

                        @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">
                            {{ $sucursal->name }}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error for="requisicion.sucursal_id" />
                </div>

          
                <div>
                    <x-label for="nombre">Nomenclatura</x-label>
                    <x-input class="w-full" wire:model="consecutivo" type="text" id="nombre" name="nombre" />
                </div>

                <div class="w-full mt-4">
                    <x-label for="nombre">{{$ultimoProducto}}</x-label>
                </div>
             



        </x-slot>

        <x-slot name='footer'>
            <x-button class="bg-blue-500 hover:pointer text-white font-bold" type="submit">Buscar</x-button>
           
            </form>
            <x-button wire:click="$set('openConsecutivo', false) " class="bg-blue-500 hover:pointer text-white font-bold" >Salir</x-button>
        </x-slot>

    </x-dialog-modal>
</div>
