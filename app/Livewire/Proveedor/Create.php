<?php

namespace App\Livewire\Proveedor;

use App\Models\Sucursal;
use App\Service\ApiUrl;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{

    use LivewireAlert;

    public $sucursales = [];
    public $sucursal_id = '';
    public $open = false;

    public $urlApi;

    //RequesicionCrear
    #[Rule([
        'sucursal_id' => 'required',
        'proveedor.crazonsocial' => 'required|min:3',
        'proveedor.crfc' => 'required|max:20',
        'proveedor.cemail1' => 'required|max:60'

    ], [], [
        'proveedor.crazonsocial' => 'Razon Social',
        'proveedor.crfc' => 'RFC',
        'proveedor.cemail1' => 'Correo electronico',
        'sucursal_id' => 'Sucursal'
    ])]

    public $proveedor = [
        'crazonsocial' => '',
        'crfc' => '',
        'cemail1' => ''
    ];

    public function save()
    {
        $this->validate();

        try {

            $sucursal = Sucursal::find($this->sucursal_id);


            $url = $this->urlApi  . $sucursal->nomenclatura . '/ComercialProveedor';

            $response = Http::post($url, $this->proveedor);

            if ($response->successful()) {
                $data = $response->json();
                $msj = 'Alta Proveedor Codigo Cliente: ' . $data['ccodigocliente'] . ' Cliente: ' . $data['crazonsocial'];
                $this->alert('success', $msj);
            } else {

                $msj = $response->json()['message'];
                $this->alert('error', $msj);
            }
        } catch (\Exception $e) {
            // Aquí manejas otras excepciones que puedan ocurrir
            $this->alert('error', 'Ocurrió un error durante la solicitud.');
        }

        $this->open = false;
    }

    public function mount()
    {
        $this->urlApi  = ApiUrl::urlApi();
        $this->sucursales = Sucursal::all();
    }

    public function render()
    {
        return view('livewire.proveedor.create');
    }
}
