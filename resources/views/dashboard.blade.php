<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    @if (Auth()->id() == 2)
        <div class="container mt-3 card pt-3">
            <div>
                <h5>Entre Fechas</h5>
                <div class="d-flex flex-column flex-lg-row justify-content-start">
                    <div class="mb-3 me-2">
                        <label for="init">Fecha Inicial:</label>
                        <input type="date" id="init" name="init" style="height: 2rem; border-radius: 4px;">
                    </div>
                    <div class="mb-3 me-2">
                        <label for="final" style="font-weight: 500; font-size: 1rem;">Fecha Final:</label>
                        <input type="date" id="final" name="final" style="height: 2rem; border-radius: 4px;">
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary btn-sm " onclick="renderGraficoGeneral()">Filtrar</button>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <h3>Requisiciones por Estatus</h3>
                </div>
                <div class="col-12 mb-sm-3 col-md-3 col-lg-4">
                    <ul class="list-group" id="listaestatus">
                    </ul>
                </div>

                <div id="generalChart" class="col-12 col-md-9 col-lg-8">
                    <div>
                        <p class="m-0">Total de Requisiciones: <span id="totalrequi">Cargando...</span></p>
                    </div>
                    <canvas id="myChart"></canvas>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    <h3>Requisiciones por Proveedores</h3>
                </div>
                <div id="" class="col-12 col-md-5" style="height: 70vh; overflow: auto;">
                    <table class="table">
                        <thead style="position:sticky; top: 0; left: 0;">
                            <tr>
                                <th scope="col">Proveedor</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody id="contenido-table-proveedor">

                        </tbody>
                    </table>
                </div>
                <div id="generalChartProveedores" class="col-12 col-md-7 d-flex justify-content-center" style="height: 70vh">
                    <canvas id="chartProveedores"></canvas>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    <h3>Unidades con mas Requisiciones</h3>
                </div>
                <div id="" class="col-12 col-md-3" style="height: 50vh; overflow: auto;">
                    <table class="table">
                        <thead style="position:sticky; top: 0; left: 0;">
                            <tr>
                                <th scope="col">Unidad</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody id="contenido-table-unidades">

                        </tbody>
                    </table>
                </div>
                <div id="generalChartUnidades" class="col-12 col-md-9">
                    <canvas id="chartUnidades"></canvas>
                </div>
            </div>
        </div>
    @endif
    <script>
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        var ctx = document.getElementById('myChart');
        var ctxProveedores = document.getElementById('chartProveedores');
        var ctxUnidades = document.getElementById('chartUnidades');

        const formatDate = async (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Asegura 2 dígitos
            const day = String(date.getDate()).padStart(2, '0'); // Asegura 2 dígitos
            return `${year}-${month}-${day}`;
        };

        const renderListaEstatusTotal = async (counts = []) => {
            $('#listaestatus').empty();
            $.each(counts, function(i, item) {
                $('#listaestatus').append('<li class="list-group-item" style="font-size: 0.8rem">' + item
                    .estatus + ': ' + item
                    .total + '</li>')
            });
        }

        const renderGraficoAllStatus = (labelsG = [], countsG = []) => {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    label: 'Total x Estatus', //labelsG.map(row => row.name), //['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    datasets: [{
                        label: 'Total x Estatus',
                        data: countsG, //[12, 19, 3, 5, 2, 3],
                        borderWidth: 1
                    }]
                },
                options: {
                    parsing: {
                        xAxisKey: 'estatus',
                        yAxisKey: 'total'
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        const renderGraficoAllProveedores = (dataProveedor = []) => {
            var totalData = dataProveedor.length
            var labelsProveedores = [];

            for (let index = 0; index < (totalData > 10 ? 10 : totalData); index++) {
                console.log(index);
                labelsProveedores.push({
                    total: dataProveedor[index].total,
                    proveedor: dataProveedor[index].proveedor
                })
            }

            var data = {
                labels: labelsProveedores.map(row => row.proveedor),
                datasets: [{
                    label: 'Total Requisiciones',
                    data: labelsProveedores.map(row => row.total),
                    backgroundColor: [
                        'rgb(255, 205, 86)',
                        'rgb(255, 0, 0)',
                        'rgb(128, 0, 0)',
                        'rgb(255, 255, 0)',
                        'rgb(128, 128, 0)',
                        'rgb(0, 128, 0)',
                        'rgb(0, 255, 255)',
                        'rgb(0, 0, 255)',
                        'rgb(0, 0, 128)',
                        'rgb(255, 0, 255)'
                    ],
                    hoverOffset: 4
                }]
            };
            new Chart(ctxProveedores, {
                type: 'pie', //'doughnut',
                data: data,
            });
        }

        const renderGraficoAllUnidades = (dataUnidades = []) => {
            new Chart(ctxUnidades, {
                type: 'bar',
                data: {
                    label: 'Total x Unidades', //labelsG.map(row => row.name), //['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    datasets: [{
                        label: 'Total Requisiciones x Unidades',
                        data: dataUnidades, //[12, 19, 3, 5, 2, 3],
                        borderWidth: 1
                    }]
                },
                options: {
                    parsing: {
                        xAxisKey: 'unidad',
                        yAxisKey: 'total'
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        const sumaTotales = (contadores = []) => {
            var total = 0;
            contadores.forEach(element => {
                total = total + element.total
            });
            return total;
        }

        const generarGraficosEstatus = async (fd, ld) => {
            $.ajax({
                url: window.location.origin + `/graficos/all/status?fd=${fd}&ld=${ld}`,
                type: 'GET',
                success: function(resp) {
                    console.log(resp);
                    renderGraficoAllStatus(resp.estatus, resp.contador);
                    $('#totalrequi').text(sumaTotales(resp.contador));
                    renderListaEstatusTotal(resp.contador)

                },
                error: function(err) {
                    console.error('Error:', err);
                }
            });
        }

        const generarGraficosProveedores = async (fd, ld) => {
            $.ajax({
                url: window.location.origin + `/graficos/all/proveedores?fd=${fd}&ld=${ld}`,
                type: 'GET',
                success: function(resp) {
                    renderTablaProveedor(resp.proveedores)
                    renderGraficoAllProveedores(resp.proveedores)
                },
                error: function(err) {
                    console.error('Error:', err);
                }
            });
        }

        const generarGraficosUnidades = async (fd, ld) => {
            $.ajax({
                url: window.location.origin + `/graficos/all/unidades?fd=${fd}&ld=${ld}`,
                type: 'GET',
                success: function(resp) {
                    console.log(resp);
                    renderTablaUnidades(resp)
                    renderGraficoAllUnidades(resp)

                },
                error: function(err) {
                    console.error('Error:', err);
                }
            });
        }

        const generarGraficos = async (fd, ld) => {
            await generarGraficosEstatus(fd, ld);
            await generarGraficosProveedores(fd, ld);
            await generarGraficosUnidades(fd, ld);
        }

        const renderDivChar = async () => {
            $('#generalChart').empty();
            $('#generalChart').html(`<canvas id="myChart"></canvas>`)
            $('#generalChartProveedores').empty();
            $('#generalChartProveedores').html(`<canvas id="chartProveedores"></canvas>`)
            $('#generalChartUnidades').empty();
            $('#generalChartUnidades').html(`<canvas id="chartUnidades"></canvas>`)
        }

        const renderGraficoGeneral = async () => {
            await renderDivChar()
            ctx = document.getElementById('myChart');
            ctxProveedores = document.getElementById('chartProveedores');
            ctxUnidades = document.getElementById('chartUnidades')
            var fd = document.getElementById('init').value;
            var ld = document.getElementById('final').value;
            await generarGraficos(fd, ld);

        }

        const renderTablaProveedor = (data = []) => {
            $('#contenido-table-proveedor').empty();
            $.each(data, function(i, item) {
                $('#contenido-table-proveedor').append(`
                <tr>
                <th style="font-size: 0.8rem">${item.proveedor}</th>
                <td>${item.total}</td>
                </tr>
                `)
            });

        }

        const renderTablaUnidades = (data = []) => {
            $('#contenido-table-unidades').empty();
            $.each(data, function(i, item) {
                $('#contenido-table-unidades').append(`
                <tr>
                <th style="font-size: 0.8rem">${item.unidad}</th>
                <td>${item.total}</td>
                </tr>
                `)
            });

        }

        document.addEventListener("DOMContentLoaded", async function() {
            // Tu código JavaScript aquí
            console.log("El DOM está completamente cargado");
            document.getElementById('init').value = await formatDate(firstDay);
            document.getElementById('final').value = await formatDate(lastDay);
            var fd = document.getElementById('init').value;
            var ld = document.getElementById('final').value;
            await generarGraficos(fd, ld);

        });
    </script>
    {{-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

            </div>
        </div>
    </div> --}}
</x-app-layout>
