<div class="p-2">

    <div class="relative overflow-x-auto rounded-lg">
        <table class="w-full  text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="text-md font-bold text-gray-700 uppercase bg-gray-200 ">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center">
                        {{__('Id')}}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        {{__('Name')}}
                    </th>

                </tr>
            </thead>
            <tbody>
                @forelse ($empresas as $empresa)
                <tr class="bg-white border">
                    <td class="px-6 py-3 text-center">{{ $empresa->id }}</td>
                    <td class="px-6 py-3 text-center">{{ $empresa->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-6 py-3 text-center">Aun no cuentas con empresas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>


    </div>






</div>