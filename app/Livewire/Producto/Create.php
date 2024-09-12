<?php

namespace App\Livewire\Producto;


use App\Models\Sucursal;
use App\Service\ApiUrl;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;


class Create extends Component
{

    use LivewireAlert;

    public $open = false;
    public $openConsecutivo = false;
    public $sucursales = [];
    public $sucursal_id = '';
    public $clasificacion1 = [];
    public $clasificacion2 = [];
    public $listaunidadmedidas=[];
    public $consecutivo='';
    public $ultimoProducto='';

    public $urlApi;

    #[Rule([
        'sucursal_id' => 'required',
        'producto.CCODIGOPRODUCTO' => 'required|min:3',
        'producto.CNOMBREPRODUCTO' => 'required|max:50',
        'producto.CIDUNIXML' => 'required',
        'producto.CIDVALORCLASIFICACION1' => 'required|max:20',
        'producto.CIDVALORCLASIFICACION2' => 'required|max:20'


    ], [], [
        'sucursal_id' => 'Sucursal',
        'producto.CCODIGOPRODUCTO' => 'codigo producto',
        'producto.CNOMBREPRODUCTO' => 'nombre producto',
        'producto.CIDUNIXML' => 'unidad medida',
        'producto.CIDVALORCLASIFICACION1' => 'tipo producto',
        'producto.CIDVALORCLASIFICACION2' => 'consumo frecuente'

    ])]
    public $producto = [
        'CCODIGOPRODUCTO' => "",
        'CNOMBREPRODUCTO' => "",
        'CIDVALORCLASIFICACION1' => "",
        'CIDVALORCLASIFICACION2' => "",
        'CIDUNIXML'=>''
    ];


    #[On('AbrirModalAltaProducto')]
    public function AbrirModal()
    {
        $this->open = true;
    }

    public function mount()
    {
        $this->urlApi = ApiUrl::urlApi();
        $this->sucursales = Sucursal::all();
    }

    public function updated($property)
    {
        if ($property === 'sucursal_id') {
            $sucursal = Sucursal::find($this->sucursal_id);

            $response1 = Http::get($this->urlApi  . $sucursal->nomenclatura . '/ComercialClasificacionValores/25');
            $this->clasificacion1 = $response1->json();

            $response2 = Http::get($this->urlApi  . $sucursal->nomenclatura . '/ComercialClasificacionValores/26');
            $this->clasificacion2 = $response2->json();

            $response3 = Http::get($this->urlApi  . $sucursal->nomenclatura . '/ComercialUnidadMedida');
            $this->listaunidadmedidas = $response3->json();
        }
    }
    public function save()
    {
        $this->validate();

      

        try {
            $sucursal = Sucursal::find($this->sucursal_id);
            $url = $this->urlApi  . $sucursal->nomenclatura . '/ComercialProductos';

            $response = Http::post($url, $this->producto);

            if ($response->successful()) {
                $data = $response->json();
                $msj = 'Alta Producto Codigo : ' . $data['ccodigoproducto'] . ' Producto: ' . $data['cnombreproducto'];
                $this->alert('success', $msj);
                $this->reset(['producto', 'sucursal_id']);
            } else {

                $msj = $response->json()['message'];
                $this->alert('error', $msj);
            }
        } catch (\Throwable $th) {
            $this->alert('error', 'Ocurrió un error durante la solicitud.');
        }

        $this->open = false;
    }

    public function buscarconsecutivo(){
        $sucursal = Sucursal::find($this->sucursal_id);
        $producto = Http::get($this->urlApi  . $sucursal->nomenclatura . '/ComercialProductos/Consecutivo?nomenclatura='.$this->consecutivo);
      

        if($producto->successful()){
            $response = $producto->json();
            $this->ultimoProducto = 'El ultimo codigo de producto registrado con la nomenclatura '.$this->consecutivo. ' es '. $response['ccodigoproducto'] .'-'. $response['cnombreproducto'];
        }else{
            $this->ultimoProducto='No se encontraron resultados con la nomenclatura ';
        }
       
    }
    public function render()
    {
        return view('livewire.producto.create');
    }
}
