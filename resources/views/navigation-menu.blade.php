<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 px-3  w-100 d-flex align-items-center h-20" style="position: sticky; top:0; z-index:20;">
    <!-- Primary Navigation Menu -->
    <!-- Logo -->
    <div id="cont-logo" class="w-1/4">
        {{-- <a href="{{ route('dashboard') }}"> --}}
        <x-application-mark wire:click="route('dashboard')" class="block" />
        {{-- </a> --}}
    </div>

    {{-- VISTA SITEMAS --}}
    @if (auth()->user()->departamento_id == 1)
        <div id="cont-pages" class="flex xl:justify-between md:justify-end w-3/4">
            <div class="hidden md:flex justify-center align-middle lg:mx-2 md:mx-2">
                {{-- <x-nav-link :active="request()->routeIs('usuario.index')"> --}}
                <a class="nav-link dropdown-toggle ms-0 pt-1" data-bs-toggle="dropdown" href="#" role="button"
                    aria-expanded="false"><span class="text-gray-500 font-medium text-sm"
                        style="text-decoration: underline">Catalogo
                        Interno</span></a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('usuario.index') }}">Usuarios</a></li>
                    <li><a class="dropdown-item" href="{{ route('estatus.index') }}">Estatus</a></li>
                    <li><a class="dropdown-item" href="{{ route('departamento.index') }}">Departamentos</a></li>
                    <li><a class="dropdown-item" href="{{ route('puesto.index') }}">Puestos</a></li>
                    <li><a class="dropdown-item" href="{{ route('gastosfijos.index') }}">Gastos Fijos</a></li>
                    <li class="hidden md:flex xl:hidden"><a class="dropdown-item"
                            href="{{ route('permisosrequisicion.index') }}">{{ __('Flujo autorización') }}</a></li>
                    <li class="hidden md:flex lg:hidden"><a class="dropdown-item"
                            href="{{ route('empresa.index') }}">{{ __('Empresas') }}</a></li>
                    <li class="hidden md:flex lg:hidden"><a class="dropdown-item"
                            href="{{ route('sucursal.index') }}">{{ __('Sucursales') }}</a></li>
                </ul>
                {{-- </x-nav-link> --}}
            </div>
            <div class="hidden xl:flex justify-center align-middle">
                <x-nav-link href="{{ route('permisosrequisicion.index') }}" :active="request()->routeIs('permisosrequisicion.index')">
                    {{ __('Flujo autorización') }}
                </x-nav-link>
            </div>
            <div class="hidden lg:flex xl:flex justify-center align-middle lg:mx-2">
                <x-nav-link href="{{ route('empresa.index') }}" :active="request()->routeIs('empresa.index')">
                    {{ __('Empresas') }}
                </x-nav-link>
            </div>
            <div class="hidden lg:flex xl:flex justify-center align-middle lg:mx-2">
                <x-nav-link href="{{ route('sucursal.index') }}" :active="request()->routeIs('sucursal.index')">
                    {{ __('Sucursales') }}
                </x-nav-link>
            </div>
            <div class="hidden md:flex justify-center align-middle md:mx-2">
                <x-nav-link href="{{ route('proveedor.index') }}" :active="request()->routeIs('proveedor.index')">
                    {{ __('Proveedores') }}
                </x-nav-link>
            </div>
            <div class="hidden md:flex justify-center align-middle md:mx-2">
                <x-nav-link href="{{ route('requisicion.index') }}" :active="request()->routeIs('requisicion.index')">
                    {{ __('Requisiciones') }}
                </x-nav-link>
            </div>
        </div>
    @endif
    {{-- FIN VISTA SISTEMAS --}}

    {{-- VISTA PARA COMPRAS --}}
    @if (auth()->user()->departamento_id == 2)
        <div class="flex justify-end w-3/4">
            <div class="hidden md:flex justify-center align-middle mx-2">
                <x-nav-link href="{{ route('proveedor.index') }}" :active="request()->routeIs('proveedor.index')">
                    {{ __('Proveedores') }}
                </x-nav-link>
            </div>
            <div class="hidden md:flex justify-center align-middle mx-2">
                <x-nav-link href="{{ route('requisicion.index') }}" :active="request()->routeIs('requisicion.index')">
                    {{ __('Requisiciones') }}
                </x-nav-link>
            </div>
        </div>
    @endif
    {{-- FIN VISTA COMPRAS --}}

    {{-- VISTA PARA LOS DEMAS --}}
    @if (auth()->user()->departamento_id != 2 && auth()->user()->departamento_id != 1)
        <div class="flex justify-end w-3/4">
            <div class="hidden md:flex justify-center align-middle me-3">
                <x-nav-link href="{{ route('requisicion.index') }}" :active="request()->routeIs('requisicion.index')">
                    {{ __('Requisiciones') }}
                </x-nav-link>
            </div>
        </div>
    @endif
    {{-- FIN VISTA PARA LOS DEMAS --}}

    <div id="cont-notif" class="w-1/4">
        <div class="hidden md:flex md:items-center md:ms-3 ">
            <!-- Teams Dropdown -->
            <div>
                @livewire('notification.notification')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="60">
                        <x-slot name="trigger">
                            <span class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                    {{ Auth::user()->currentTeam->name }}

                                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>
                                </button>
                            </span>
                        </x-slot>

                        <x-slot name="content">
                            <div class="w-60">
                                <!-- Team Management -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Manage Team') }}
                                </div>

                                <!-- Team Settings -->
                                <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                    {{ __('Team Settings') }}
                                </x-dropdown-link>

                                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                    <x-dropdown-link href="{{ route('teams.create') }}">
                                        {{ __('Create New Team') }}
                                    </x-dropdown-link>
                                @endcan

                                <!-- Team Switcher -->
                                @if (Auth::user()->allTeams()->count() > 1)
                                    <div class="border-t border-gray-200"></div>

                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Switch Teams') }}
                                    </div>

                                    @foreach (Auth::user()->allTeams() as $team)
                                        <x-switchable-team :team="$team" />
                                    @endforeach
                                @endif
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            @endif

            <!-- Settings Dropdown -->
            <div class="ms-3 relative">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <button
                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="h-8 w-8 rounded-full object-cover"
                                    src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </button>
                        @else
                            <span class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                    {{ Auth::user()->name }}

                                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </span>
                        @endif
                    </x-slot>

                    <x-slot name="content">
                        <!-- Account Management -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Manage Account') }}
                        </div>

                        <x-dropdown-link href="{{ route('profile.show') }}">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                {{ __('API Tokens') }}
                            </x-dropdown-link>
                        @endif

                        <div class="border-t border-gray-200"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf

                            <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>


    <!-- Hamburger -->
    <div class="-me-2 flex items-center md:hidden">
        <div>
            @livewire('notification.notification')
        </div>
        <button class="navbar-toggler mx-2 bg-gray-100 hover:bg-gray-200 rounded" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"
            aria-label="Toggle navigation">
            <svg style="width: 2.3rem; height: 2.3rem;" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
    {{-- SIDEBAR  --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel"
        style="width: 70%!important">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">MENU</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                @if (auth()->user()->departamento_id == 1)
                    <li class="nav-item">
                        <a href="{{ route('usuario.index') }}" class="nav-link">
                            {{ __('Usuarios') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('estatus.index') }}" class="nav-link">
                            {{ __('Estatus') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('departamento.index') }}" class="nav-link">
                            {{ __('Departamentos') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('puesto.index') }}" class="nav-link">
                            {{ __('Puestos') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('permisosrequisicion.index') }}" class="nav-link">
                            {{ __('Flujo autorización') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('proveedor.index') }}" class="nav-link">
                            {{ __('Proveedores') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('requisicion.index') }}" class="nav-link">
                            {{ __('Requisiciones') }}
                        </a>
                    </li>
                @endif
                @if (auth()->user()->departamento_id == 2)
                    <li class="nav-item">
                        <a href="{{ route('proveedor.index') }}" class="nav-link">
                            {{ __('Proveedores') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('requisicion.index') }}" class="nav-link">
                            {{ __('Requisiciones') }}
                        </a>
                    </li>
                @endif
                @if (auth()->user()->departamento_id != 2 && auth()->user()->departamento_id != 1)
                    <li class="nav-item">
                        <a href="{{ route('requisicion.index') }}" class="nav-link">
                            {{ __('Requisiciones') }}
                        </a>
                    </li>
                @endif

                <hr>
                <p class="text-gray-400 mb-1"> {{ __('Manage Account') }} </p>
                <li class="nav-item">
                    <a href="{{ route('profile.show') }}" class="nav-link">
                        {{ __('Profile') }}
                    </a>
                </li>
                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <li class="nav-item">
                        <x-responsive-nav-link class="nav-link ps-0" href="{{ route('api-tokens.index') }}"
                            :active="request()->routeIs('api-tokens.index')">
                            {{ __('API Tokens') }}
                        </x-responsive-nav-link>
                    </li>
                @endif
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-dropdown-link href="{{ route('logout') }}" class="nav-link ps-0"
                            @click.prevent="$root.submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </li>


            </ul>

        </div>
    </div>

</nav>
