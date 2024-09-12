<div class="p-2">

    <div class="relative overflow-x-auto rounded-lg">
        <table class="w-full  text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="text-xs text-gray-700 uppercase bg-gray-200 ">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center">
                        {{__('Id')}}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        {{__('Name')}}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        {{__('Empresa')}}
                    </th>

                </tr>
            </thead>
            <tbody>
                @forelse ($sucursales as $sucursal)
                <tr class="bg-white border">
                    <td class="px-6 py-3 text-center">{{ $sucursal->id }}</td>
                    <td class="px-6 py-3 text-center">{{ $sucursal->name }}</td>
                    <td class="px-6 py-3 text-center">{{ $sucursal->empresa->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-3 text-center">Aun no cuentas con sucursales registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>


    </div>


</div>