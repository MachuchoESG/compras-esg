<div wire:id="unique-id-xyz">
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="mt-3 mb-3 ps-3 pe-3 d-flex justify-content-between align-items-center">
        <div class="row">
            <div class="col">
                <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="Buscar">
            </div>
            <div class="col">
                <select id="f_empresa"
                    class="border-1 border-slate-900 rounded-md  text-black text-md capitalize form-control">
                    <option value="0" selected disabled>
                        Filtro Empresa
                    </option>
                    @foreach ($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <select id="f_sucursal" placeholder="Seleccione una sucursal"
                    class="border-1 border-slate-900 rounded-md text-black text-md capitalize form-control">
                    <option value="0" selected disabled>
                        Filtro Sucursal
                    </option>

                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}-{{ $sucursal->nomenclatura }}">
                            {{ $sucursal->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>
        <div class="">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-gf-modal">
                Agregar Nuevo
            </button>{{-- <button class="btn btn-primary" wire:click="$set('showModalAgregarGF', true)">Agregar Nuevo</button> --}}
        </div>
    </div>

    <div class="row mt-3 mb-3 ps-3 pe-3">
        <div class="col-12">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Producto</th>
                        <th scope="col">Empresa</th>
                        <th scope="col">Sucursal</th>
                        <th scope="col">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gastosfijos as $gf)
                        <tr>
                            <th scope="row">{{ $gf->id }}</th>
                            <td>{{ $gf->producto_name }}</td>
                            <td>{{ $gf->empresa->name }}</td>
                            <td>{{ $gf->sucursal->name }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                    onclick="sendBorrarGF({{ $gf->id }})">Borrar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="add-gf-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Gasto Fijo</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div>
                                <label class="form-label" for="idempresa">Empresa</label>
                                <select id="idempresa"
                                    class="border-1 w-100 border-slate-900 rounded-md  text-black text-md capitalize">
                                    <option value="0" selected disabled>
                                        Seleccione una empresa
                                    </option>
                                </select>
                            </div>

                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label" for="idsucursal">Sucursal</label>
                            <select placeholder="Seleccione una sucursal" id="idsucursal"
                                class="border-1 w-100 border-slate-900 rounded-md text-black text-md capitalize">
                                <option value="0" selected disabled>
                                    Seleccione una sucursal
                                </option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label" for="producto_gasto_fijo">Producto</label>
                            <br>
                            <select id="producto_gasto_fijo" style="width: 100%important;"
                                class="border-1 border-slate-900 rounded-md" required>
                                <option value="0">Seleccione un producto</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="sendGastoFijo()">Agregar</button>
                </div>
            </div>
        </div>
    </div>


    {{-- <x-dialog-modal id="modalGF" wire:model='showModalAgregarGF'>
        <x-slot name='title'>
            Asignar como Gasto Fijo
        </x-slot>
        <x-slot name='content'>
            <div class="row">
                <div class="col-7 col-lg-5 mb-3">
                    <label for="idempresa">Empresa</label>
                    <select id="idempresa"
                        class="border-1 border-slate-900 rounded-md  text-black text-md capitalize">
                        <option value="0" selected disabled>
                            Seleccione una empresa
                        </option>
                    </select>
                </div>
                <div class="col-7 col-lg-5 mb-3">
                    <label for="idsucursal">Sucursal</label>
                    <select
                        placeholder="Seleccione una sucursal"
                        class="border-1 border-slate-900 rounded-md text-black text-md capitalize">
                        <option value="0" selected disabled>
                            Seleccione una sucursal
                        </option>
                    </select>
                </div>
                <div class="col-7 col-lg-5 mb-3">
                    <label for="">Producto</label>

                    <select id="producto_gasto_fijo"
                        class="border-1 border-slate-900 rounded-md" required style="max-width: 100%">
                        <option value="0">Seleccione un producto</option>
                    </select>
                </div>
            </div>

        </x-slot>

        <x-slot name='footer'>
            <x-button class="me-3" wire:click="$set('showModalAgregarGF', false)">Salir</x-button>
            <div class="">
                <button wire:click="agregarGastoFijo()" class="btn btn-primary">
                    Agregar
                </button>
            </div>
        </x-slot>
    </x-dialog-modal> --}}

    <style>
        .select2-container--default .select2-selection--single {
            height: 2.5rem;
            display: flex;
            align-items: center;
            border-radius: 6px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            top: 70% !important
        }
    </style>
</div>
<script>
    var ALLSUCURSALES = [];

    function sendBorrarGF(id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: window.location.origin + "/gastosfijos/" + id,
            type: 'DELETE',
            success: function(res) {
                //$('#add-gf-modal').modal('hide')
                //@this.call('successAlert');
                window.location.reload()
            },
            error: function(err) {
                console.error('Error:', err);
            }
        });
    }

    function sendGastoFijo() {
        var arrValSuc = ($('#idsucursal').val()).split('-')
        var dataPost = {
            id_empresa: Number($('#idempresa').val()),
            id_sucursal: Number(arrValSuc[0]),
            id_producto: Number($('#producto_gasto_fijo').val()),
            producto_name: $('#producto_gasto_fijo option:selected').text()
        }

        sendPostProducts(dataPost);

    }

    function renderOptionsSucursales(id) {
        $("#idsucursal").empty().append('<option value="0">Seleccione una Sucursal</option>');
        $("#producto_gasto_fijo").empty().append('<option value="0">Seleccione un Producto</option>');

        ALLSUCURSALES.forEach(element => {
            if (element.id === Number(id)) {
                element.sucursales.forEach(sucursal => {
                    $('#idsucursal').append($('<option>', {
                        value: sucursal.id + '-' + sucursal.nomenclatura,
                        text: sucursal.name
                    }))
                })
            }
        });
    }

    function renderOptionsEmpresas(resp) {

        $.each(resp, function(i, item) {
            ALLSUCURSALES.push({
                id: item.id,
                sucursales: item.sucursales
            })
            $('#idempresa').append($('<option>', {
                value: item.id,
                text: item.name
            }))
        });

    }

    function renderOptionsProductos(resp) {
        $("#producto_gasto_fijo").empty().append('<option value="0">Seleccione un Producto</option>');
        $.each(resp, function(i, item) {
            $('#producto_gasto_fijo').append($('<option>', {
                value: item.cidproducto,
                text: item.cnombreproducto
            }))
        });
    }

    function sendPostProducts(data) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: window.location.origin + "/gastosfijos",
            type: 'POST',
            data: data,
            success: function(res) {
                $('#add-gf-modal').modal('hide')
                window.location.reload()

            },
            error: function(err) {
                $('#add-gf-modal').modal('hide')
                console.error('Error:', err);
                window.location.reload()
            }
        });
    }

    function getAllProducts(opt) {
        $.ajax({
            url: window.location.origin + "/gf-opt-productos/" + opt,
            type: 'GET',
            success: function(res) {
                console.log(res);

                renderOptionsProductos(res)
            }
        });
    }

    function getAllEmpresas() {
        $.ajax({
            url: window.location.origin + "/gf-empresas",
            type: 'GET',
            success: function(res) {
                console.log(res);
                renderOptionsEmpresas(res)
            }
        });
    }

    function renderModal() {
        $("#idsucursal").empty().append('<option value="0">Seleccione una Sucursal</option>');
        $("#producto_gasto_fijo").empty().append('<option value="0">Seleccione un Producto</option>');
        getAllEmpresas()
        $('#producto_gasto_fijo').select2({
            dropdownParent: $('#add-gf-modal')
        });
    }

    //DATOS CAMBIANTES
    $('#idsucursal').on('change', function(e) {
        var valueSuc = e.target.value;
        getAllProducts(valueSuc)
        //renderOptionsSucursales(idEmp);
    })

    $('#idempresa').on('change', function(e) {
        var idEmp = e.target.value;
        renderOptionsSucursales(idEmp);
    })

    $('#producto_gasto_fijo').select2({
        dropdownParent: $('#add-gf-modal')
    });

    //CARGA DE DATOS INICIAL
    getAllEmpresas()
</script>
