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
                            <button onclick="openEditarModal({{ $item }})" type="button"
                                class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#estatusmodal">
                                Editar
                            </button>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    <div class="modal fade" id="estatusmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="estatusmodal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Estatus: <span id="title_estatus"></span></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input id="input_id"/>
                    <div class="mb-3">
                    <label for="input_descripcion" class="form-label">DESCRIPCIÓN:</label>
                    <textarea class="form-control" id="input_descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let estatusSelected = null

        function openEditarModal(estatus) {
            //console.log(id);
            estatusSelected = estatus
            $('#title_estatus').text(estatusSelected.name ?? 'Cargando...');
            $('#input_descripcion').val(estatusSelected.descripcion ?? '')
            $('#input_id').val(estatusSelected.id ?? '')
            console.log(estatusSelected);

        }

        function sendUpdateEstatus(){
            @this.call('updateEstatus', $('#input_id').val(), $('#input_descripcion').val());
        }
    </script>
</div>
