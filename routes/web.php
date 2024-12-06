<?php

use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\empresaController;
use App\Http\Controllers\EstatusController;
use App\Http\Controllers\GastosFijosController;
use App\Http\Controllers\GraficosController;
use App\Http\Controllers\MigrationsRun;
use App\Http\Controllers\PermisosrequisicionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\proveedorController;
use App\Http\Controllers\PuestoController;
use App\Http\Controllers\requisicionController;
use App\Http\Controllers\sucursalController;
use App\Http\Controllers\UsuarioController;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');
Route::get('/', function () {
    return view('welcome');
});
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    Route::resource('empresa', empresaController::class);
    Route::resource('sucursal', sucursalController::class);
    Route::resource('proveedor', proveedorController::class);
    Route::resource('producto', ProductoController::class);
    Route::get('/productos/asignar', [ProductoController::class, 'getProductosParaAsignar']);
    Route::post('/productos/asignar/{id}', [ProductoController::class, 'asignarIdProducto']);
    Route::resource('requisicion', requisicionController::class);
    Route::get('/{nom}/producto/{id}/existencia', [requisicionController::class, 'getTotalExistenciaProducto']);
    Route::resource('departamento', DepartamentoController::class);
    Route::resource('puesto', PuestoController::class);
    Route::resource('usuario', UsuarioController::class);
    Route::resource('estatus', EstatusController::class);
    Route::resource('gastosfijos', GastosFijosController::class);
    Route::get('gf-empresas', [GastosFijosController::class, 'gastosFijosEmpresas']);
    Route::get('gf-opt-productos/{opt}', [GastosFijosController::class, 'gastosFijosOptProductos']);
    Route::resource('permisosrequisicion', PermisosrequisicionController::class);
    Route::get('/requisicion/{requisicion}/autorizar', [requisicionController::class, 'autorizar'])->name('requisicion.autorizar');
    Route::get('/requisicion/{requisicion}/aprobacion', [requisicionController::class, 'aprobacion'])->name('requisicion.aprobacion');
    Route::get('/requisicion/{requisicion}/formato', [requisicionController::class, 'formato'])->name('requisicion.formato');
    Route::resource('cotizacion', CotizacionController::class);
    Route::get('/run-migrations', [App\Http\Controllers\MigrationsRun::class, 'runMigrations']);
    Route::get('/clear-view-cache', [App\Http\Controllers\MigrationsRun::class, 'clearViewCache']);

    //GRAFICOS
    Route::get('/graficos/all/status', [GraficosController::class, 'GraficoAllRequisicionesStatus']);
    Route::get('/graficos/all/proveedores', [GraficosController::class, 'GraficoAllRequisicionesProveedores']);
    Route::get('/graficos/all/unidades', [GraficosController::class, 'GraficoAllRequisicionesUnidades']);
    Route::get('/graficos/all/unidades/gastos', [GraficosController::class, 'GraficoAllGastosPorUnidades']);
});
