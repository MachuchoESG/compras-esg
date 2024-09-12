<?php

namespace App\Livewire\Producto;

use App\Models\Sucursal;
use App\Service\ApiUrl;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use LivewireAlert;

    public $productos = [];
    public $open = false;


    public $producto = [
        'id_Producto' => '',
        'cantidad' => '',
        'producto' => '',
        'observaciones' => '',
    ];

    public $urlApi;

    #[On('sucursal-selected')]
    public function updateEProductosList($sucursal)
    {

        $response = Http::get($this->urlApi  . $sucursal['nomenclatura'] . '/ComercialProductos');
        $this->productos = $response->json();
    }

    public function mount()
    {
        $this->urlApi = ApiUrl::urlApi();
    }
    public function save()
    {
        $this->validate([
            'producto.id_Producto' => 'required',
            'producto.cantidad' => 'required',
            'producto.observaciones' => 'required',
        ], [], [
            'producto.id_Producto' => 'Producto',
            'producto.cantidad' => 'Cantidad',
            'producto.observaciones' => 'Observaciones',
        ]);

        $this->dispatch('addProduct', $this->producto);
    }


    public function render()
    {
        return view('livewire.producto.index');
    }
}
