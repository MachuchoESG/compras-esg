<?php

namespace App\Livewire\GastosFijos;

use App\Http\Controllers\GastosFijosController;
use App\Models\Empresa;
use App\Models\GastoFijo;
use App\Service\ProductoService;
use App\Service\ProveedorService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Index extends Component
{
    use LivewireAlert;
    public $gastosfijos = [];
    public $provedores = [];
    public $sucursales = [];
    public $productos = [];
    public $empresas = [];
    public $empresa_id = 0;
    public $sucursal_id = 0;
    public $producto_pago_fijo = 0;
    public $showModalAgregarGF = false;

    protected $listeners = ['agregarGastoFijo'];


    protected function obtenerSucursales($empresaId)
    {
        $empresa = Empresa::find($empresaId);
        if ($empresa) {
            $this->sucursales = $empresa->sucursales;
            //dd($this->sucursales);
        }
    }

    public function obtenerProductos($value)
    {
        $productService = new ProductoService();
        $arr = explode('-', $value);
        $this->productos = $productService->ListaProductos($arr[1]);
    }

    public function empresaChange()
    {
        $this->obtenerSucursales($this->empresa_id);
    }

    public function successAlert()
    {
        $this->alert('success', 'Se agrego producto correctamente a Gastos Fijos.');
    }

    public function mount()
    {
        $this->empresas = Empresa::with('sucursales')->get();
        $this->gastosfijos = GastoFijo::with(['empresa', 'sucursal'])->get(); // GastoFijo::all();
        //dd($this->gastosfijos);
    }

    public function render()
    {
        if (session('flag_success')) {
            $this->alert('success', 'Se agrego producto correctamente a Gastos Fijos.');
            session()->forget('flag_success');
        }

        if (session('flag_error')) {
            $this->alert('error', 'Fallo al agregar producto a Gastos Fijos.');
            session()->forget('flag_error');
        }
        
        return view('livewire.gastosfijos.index');
    }
}
