<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ESG') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

</head>

<body class="font-sans antialiased bg-gray-100 w-100" style="">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    {{--     @stack('script')
 --}}

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>

    {{-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script>
        function mostrarNotificacion(mensaje) {
            console.log('ejecutar Notification');

            const options = {
                body: mensaje, // Texto de la notificación
                icon: 'https://esg.com.mx/wp-content/uploads/2019/07/logo-esg-2023mex.png' // Icono que acompañará la notificación
            };

            new Notification('Título de la notificación', options);
        }

        function construirNotificaciones(data) {
            var resp = {
                "pendientesaprobar": [{
                        "id": 1490,
                        "folio": "Mty-1487"
                    },
                    {
                        "id": 1491,
                        "folio": "Mty-1488"
                    },
                    {
                        "id": 1492,
                        "folio": "Mty-1489"
                    },
                    {
                        "id": 1493,
                        "folio": "Mty-1490"
                    },
                    {
                        "id": 1494,
                        "folio": "Mty-1491"
                    },
                    {
                        "id": 1495,
                        "folio": "Mty-1492"
                    },
                    {
                        "id": 1496,
                        "folio": "Mty-1493"
                    },
                    {
                        "id": 1497,
                        "folio": "Mty-1494"
                    },
                    {
                        "id": 1498,
                        "folio": "Mty-1495"
                    },
                    {
                        "id": 1499,
                        "folio": "Mty-1496"
                    },
                    {
                        "id": 1500,
                        "folio": "Mty-1497"
                    },
                    {
                        "id": 1501,
                        "folio": "Mty-1498"
                    },
                    {
                        "id": 1502,
                        "folio": "Mty-1499"
                    },
                    {
                        "id": 1503,
                        "folio": "Mty-1500"
                    },
                    {
                        "id": 1504,
                        "folio": "Mty-1501"
                    },
                    {
                        "id": 1505,
                        "folio": "Mty-1502"
                    },
                    {
                        "id": 1506,
                        "folio": "Mty-1503"
                    },
                    {
                        "id": 1507,
                        "folio": "Mty-1504"
                    },
                    {
                        "id": 1508,
                        "folio": "Mty-1505"
                    }
                ],
                "pendienteautorizar": [],
                "pendientecotizacion": [],
                "pendienteIncompletas": [],
                "pendienteAutorizarCotizacion": [],
                "totalNotificaciones": 19,
                "sizeNotification": 20
            }

            $('#counter-notifications').text(data.totalNotificaciones)
            if (data.pendientesaprobar) {
                var initString = `<div class="col block px-2 py-2 text-xs text-gray-400">`;
                var contentString = '';
                var endString = `</div>`;
                if (data.pendientesaprobar.length > 0) {
                    data.pendientesaprobar.forEach(requisicion => {
                    const link = `<x-dropdown-link style="padding-inline: .5rem!important;" href="/cotizacion/${requisicion.id}">
                                        <p>${requisicion.folio}</p>
                                    </x-dropdown-link>`;
                        contentString += link;
                    });
                } else {
                    contentString = `<p>No hay requisiciones pendientes de subir cotizaciones.</p>`
                }
            }

            document.getElementById('content-notifications').innerHTML = initString + contentString + endString;

            if (data.pendienteautorizar) {
                
            }
            if (data.pendientecotizacion) {
                
            }
            if (data.pendienteIncompletas) {
                
            }
            if (data.pendienteAutorizarCotizacion) {
                
            }
        }

        function getDataNotificaciones() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: window.location.origin + "/notificaciones/all",
                type: 'POST',
                //data: data,
                success: function(res) {
                    console.log(res);
                    construirNotificaciones(res)

                },
                error: function(err) {
                    console.error('Error:', err);
                }
            });
        }

        function renderNotifications() {
            $('#counter-notifications').empty();
            $('#content-notifications').empty();
            getDataNotificaciones()
        }

        const token = "{{ session('tokenUser') }}";

        // Conectar al servidor Socket.IO con el token JWT
        const socket = io('http://localhost:8888', {
            query: {
                token: token
            }
        });

        socket.on('connect', () => {
            console.log('Conectado al canal privado Socket.IO');

            // Enviar un mensaje privado a otro usuario (ejemplo: usuario 3)
            const mensajePrivado = {
                toUserId: {{ Auth::id() }}, // ID del usuario destinatario
                message: 'Hola usuario {{ Auth::id() }}, este mensaje es solo para ti.'
            };

            // Enviar el mensaje al servidor
            socket.emit('mensaje-privado', mensajePrivado);
        });

        socket.on('mensaje-recibido', (data) => {
            console.log(`Mensaje recibido de usuario ${data.fromUserId}: ${data.message}`);
        });

        socket.on('disconnect', () => {
            console.log('Desconectado del canal privado Socket.IO');
        });

        socket.on('pa-todos', (data) => {
            console.log('pa todos');
        })

        socket.on('channel-user-{{ Auth::id() }}', (data) => {
            mostrarNotificacion(data.message)
            renderNotifications()
        })

        socket.on('channel-departemento-{{ session('id_departamento') }}', (data) => {
            console.log('mensaje para departamentos');
            //mostrarNotificacion(data.message)
        })

        socket.on('channel-puesto-{{ session('id_puesto') }}', (data) => {
            console.log('mensaje para puestos');
            //mostrarNotificacion(data.message)
        })
    </script>

</body>

</html>
