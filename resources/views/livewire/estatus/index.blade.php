<div class="p-3">
    <div class="row">
        <div class="col-12 d-flex justify-between mb-3">
            <div class="w-50">
                <input type="text" class="form-control " id="input_search_estatus" placeholder="Buscar...">
            </div>
            <div class="me-5">
                <button class="btn btn-primary">Agregar Estatus</button>
            </div>
        </div>
    </div>
    <div class="col-12">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Descripcion</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estatus as $item)
                    <tr>
                        <th scope="row">
                            {{ $item->id }}
                        </th>
                        <td> {{ $item->name }} </td>
                        <td> {{ $item->descripcion ?? 'Sin Descripción' }} </td>
                        <td>
                            <button class="btn btn-primary btn-sm">Editar</button>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
