<?php

namespace App\Livewire\Requisicion;

use App\Livewire\Forms\Requisicion\RequisicionCreateForm;
use App\Models\Departamento;
use App\Models\Empresa;
use App\Models\Sucursal;
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

        $requisicionCreada =  $this->requisicion->save();
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

            $this->productos = ProductoService::ListaProductos($this->sucursal->nomenclatura);

            //dd($this->productos);

            $this->productoscargados = ($this->productos !== false);

            $this->cargandoproductos = false;

            $this->unidades = UnidadService::ListaUnidades($this->sucursal->nomenclatura);
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
