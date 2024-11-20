<?php

namespace App\Livewire\Requisicion;

use App\Livewire\Forms\Requisicion\RequisicionCreateForm;
use App\Models\Departamento;
use App\Models\Empresa;
use App\Models\permisosrequisicion;
use App\Models\Sucursal;
use App\Models\Token;
use App\Models\User;
use App\Service\ApiUrl;
use App\Service\ProductoService;
use App\Service\ProyectoService;
use App\Service\UnidadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{

    use WithFileUploads;
    use LivewireAlert;
    public $sucursales = [];
    public $empresas = [];
    public $sucursal;

    public $unidades = [];
    public $solicitantes = [];
    public $productos = [];
    public $proyectos = [];
    public $departamentos = [];
    public $departamento_especial;
    public $requisicion_especial = false;
    public $productosinregistro = false;
    public $productoscargados = false;
    public $cargandoproductos = false;
    public $showProductos = true;
    public $urlApi;



    public $openUnidades = false;
    public $existencias = 0;
    public $user;

    public RequisicionCreateForm $requisicion;


    public function save()
    {
        if ($this->requisicion_especial) {
            $this->requisicion->cotizacion_especial = 1;
            $this->requisicion->departamento_especial = $this->departamento_especial;
        }

        $userToken = Token::where('user_id', Auth::id())->latest()->first();
        $requisicionCreada =  $this->requisicion->save();

        if ($requisicionCreada->estatus_id === 1) {

            $user = User::find(Auth::id());
            $permiso = permisosrequisicion::where('PuestoSolicitante_id', '=', $user->puesto->id)
                ->where('departamento_id', $user->departamento_id)
                ->first();
            //dd($permiso);
            $userAutorizador = User::where('puesto_id', '=', $permiso->PuestoAutorizador_id)->first();
            //dd(['autori'=>$userAutorizador, 'permiso'=> $permiso]);
            $this->dispatch('nueva-requisicion-creada');

            $dataPost = [
                'id_puesto_solicitante' => $user->puesto_id,
                'id_puesto_autorizador' => $permiso->PuestoAutorizador_id,
                'id_usuario_alertar' => $userAutorizador->id,
                'estatus' => $requisicionCreada->estatus->name,
                'folio' => $requisicionCreada->folio,
                'url_requisicion' => "/requisicion" . "/" . $requisicionCreada->id . '/aprobacion'
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $userToken->token,
            ])->post(
                env('SERVICE_SOCKET_HOST', 'http://laravel_notifications') . ':' . env('SERVICE_SOCKET_PORT', '8888') . '/send/requisicion-actualizada',
                $dataPost
            );

            //dd($response);
        }

        if ($requisicionCreada->estatus_id === 7 || $requisicionCreada->estatus_id === 13) {
            $user = User::find(Auth::id());
            $permiso = permisosrequisicion::where('PuestoSolicitante_id', '=', $user->puesto->id)
                ->where('departamento_id', $user->departamento_id)
                ->first();
            //dd($permiso);
            $userAutorizador = User::where('puesto_id', '=', $permiso->PuestoAutorizador_id)->first();
            if ($requisicionCreada->cotizacion_especial === 1 || $requisicionCreada->cotizacion_especial === true) {
                $dataPost = [
                    'cotizacion_especial' => true,
                    'departamento_especial' => $requisicionCreada->departamento_especial,
                    'departamento' => null,
                    'id_puesto_solicitante' => $user->puesto_id,
                    'id_puesto_autorizador' => $permiso->PuestoAutorizador_id,
                    'id_usuario_alertar' => $user->id,
                    'estatus' => $requisicionCreada->estatus->name,
                    'folio' => $requisicionCreada->folio,
                    'url_requisicion' => "/requisicion" . "/" . $requisicionCreada->id . "/especial",
                ];
            } else {
                $dataPost = [
                    'cotizacion_especial' => false,
                    'departamento_especial' => null,
                    'departamento' => 2,
                    'id_puesto_solicitante' => $user->puesto_id,
                    'id_puesto_autorizador' => $permiso->PuestoAutorizador_id,
                    'id_usuario_alertar' => null,
                    'estatus' => $requisicionCreada->estatus->name,
                    'folio' => $requisicionCreada->folio,
                    'url_requisicion' => "/cotizacion" . "/" . $requisicionCreada->id,
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $userToken->token,
            ])->post(
                env('SERVICE_SOCKET_HOST', 'http://laravel_notifications') . ':' . env('SERVICE_SOCKET_PORT', '8888') . '/send/requisicion/departamento',
                $dataPost
            );
        }
        $this->alert('success', 'Se creo correctamente la requisicion con el folio' . $requisicionCreada->folio);

        return redirect()->route('requisicion.index');
    }

    public function checkCotizacionEspecial()
    {
        $this->requisicion_especial = !$this->requisicion_especial;
    }


    public function toggleOpenUnidades()
    {
        $this->openUnidades = !$this->openUnidades;
    }


    public function addProducto($id, $producto)
    {
        $this->requisicion->addProducto($id, $producto);

        $this->reset('existencias');

        $this->dispatch('cerrar-modal');
    }


    public function deleteProducto($index, $producto)
    {
        $this->requisicion->deleteProducto($index);
    }


    public function updated($property, $value)
    {
        if ($property == 'requisicion.sucursal_id') {

            $this->obtenerProductos($value);
            $this->obtenerProyectos($value);
        }

        if ($property == 'requisicion.empresa_id') {
            $this->obtenerSucursales($value);
        }

        if ($property == "requisicion.producto.producto_id") {
            $this->existencias = ProductoService::VerificarExistencia($this->sucursal->nomenclatura, $value);
        }
    }

    protected function obtenerProductos($sucursalId)
    {
        $this->sucursal = Sucursal::find($sucursalId);
        if ($this->sucursal) {
            $this->cargandoproductos = true;

            $productos = ProductoService::ListaProductos($this->sucursal->nomenclatura);

            usort($productos, function ($a, $b) {
                return strcmp($a['cnombreproducto'], $b['cnombreproducto']);
            });

            $this->productos = $productos;
            //dd($this->productos);

            $this->productoscargados = ($this->productos !== false);

            $this->cargandoproductos = false;

            $this->unidades = UnidadService::ListaUnidades($this->sucursal->nomenclatura);
            $this->dispatch('renderProductos', ['productos' => $this->productos]);
        }
    }

    protected function obtenerProyectos($sucursalId)
    {
        $this->sucursal = Sucursal::find($sucursalId);
        if ($this->sucursal) {
            $this->proyectos = ProyectoService::ListaProyectos($this->sucursal->nomenclatura);
        }
    }



    protected function obtenerSucursales($empresaId)
    {
        $empresa = Empresa::find($empresaId);
        if ($empresa) {
            $this->sucursales = $empresa->sucursales;
        }
    }


    public function mount()
    {

        $this->departamentos = Departamento::all();
        $this->departamento_especial = $this->departamentos[0]->id;
        // $this->urlApi  = ApiUrl::urlApi();
        // Obtener el usuario autenticado
        $this->user = Auth::user();

        // Si el usuario está autenticado
        if ($this->user) {
            // Obtener el ID del departamento del usuario
            $departamento_id = $this->user->departamento->id;
            // Obtener todos los usuarios que pertenecen al mismo departamento que el usuario autenticado
            $this->solicitantes = User::where('departamento_id', $departamento_id)->get();
        }

        // Cargar ansiosamente la relación 'sucursales' de todas las empresas
        $this->empresas = Empresa::with('sucursales')->get();
    }



    public function render()
    {
        return view('livewire.requisicion.create');
    }
}
