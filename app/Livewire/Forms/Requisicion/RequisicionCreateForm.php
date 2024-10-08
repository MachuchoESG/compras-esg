<?php

namespace App\Livewire\Forms\Requisicion;

use App\Models\Autorizacionhistorial;
use App\Models\permisosrequisicion;
use App\Models\Requisicion;
use App\Models\User;
use App\Service\EnviarWhatsApp;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Form;

class RequisicionCreateForm extends Form
{



    #[Rule(
        [
            'empresa_id' => 'required',
            'sucursal_id' => 'required',
            'fecharequerida' => 'required|date|after_or_equal:today',
            'proyecto_id' => 'required',
            'observaciones' => 'required',
            'listaProductos' => 'required|array|min:1'

        ],
        [],
        [
            'empresa_id' => 'Empresa',
            'sucursal_id' => 'Sucursal',
            'fecharequerida' => 'Fecha Requerida',
            'observaciones' => 'Observaciones',
            'proyecto_id' => 'Proyecto',
            'producto.producto_id' => 'Producto',
            'producto.cantidad' => 'Cantidad',
            'producto.observaciones' => 'observaciones',
            'listaProductos' => 'Productos'
        ]
    )]


    public $user;

    //Datos para crear la requisicion
    public $observaciones = '';
    public $user_id;
    public $estatus_id = 1;
    public $sucursal_id = '';
    public $empresa_id = '';
    public $fecharequerida = '';
    public $seguimiento = false;
    public $empleado_id = '';
    public $proyecto_id = '';
    public $proyecto = '';
    public $unidad = '';
    public $image;
    public $imageKey;
    public $f = false;
    public $openProductoSR = true;



    public $listaProductos = [];
    public $producto = [
        'producto_id' => '',
        'cantidad' => '',
        'producto' => '',
        'observaciones' => '',
    ];

    public function addProducto($id = null, $producto = null)
    {
        $this->producto['producto'] = $producto;
        $this->producto['producto_id'] = ($id === null || $id==='' || $id === '0') ? 0 : $id ;
        $this->validate([
            'producto.producto_id' => 'required',
            'producto.cantidad' => 'required|numeric|min:1',
            'producto.observaciones' => 'required',

        ]);

        $this->listaProductos[] = $this->producto;
        $this->reset('producto', 'productosinregistro', 'openProductoSR');
    }

    public function deleteProducto($index)
    {
        unset($this->listaProductos[$index]);
    }

    public function save()
    {



        $this->user = Auth::user();

        $this->validate();

        $this->user_id = $this->user->id;

        if ($this->empleado_id == '') {
            $this->empleado_id = $this->user->id;
        }



        $requisicionNueva = Requisicion::create(
            $this->only(
                'empresa_id',
                'sucursal_id',
                'fecharequerida',
                'empleado_id',
                'observaciones',
                'user_id',
                'estatus_id',
                'unidad',
                'seguimiento',
                'proyecto_id',
                'proyecto'
            )
        );


        $requisicionNueva->detalleRequisiciones()->createMany($this->listaProductos);



        // Asociar la evidencia
        if ($this->image) {
            $folder = 'requisicion_' . $requisicionNueva->folio . '/evidencias';

            // Guardar la evidencia dentro de la carpeta de evidencias de la requisición
            $path = $this->image->store($folder);

            // Crear el registro de evidencia asociado a la requisición
            $requisicionNueva->evidencia()->create(['url' => $path]);
        }


        if ($requisicionNueva) {

            if (User::jefe()) {
                $requisicionNueva->aprobado = true;
                $requisicionNueva->estatus_id = 7;
                $requisicionNueva->visto = true;
                $requisicionNueva->save();

                //se agrega al historial para que cuando compras suba cotizacion le salga la notificacion
                $autorizacion = Autorizacionhistorial::create([
                    'requisicion_id' => $requisicionNueva->id,
                    'user_id' => auth()->user()->puesto_id,
                    'user_solicita' => auth()->user()->puesto_id,
                    'departamento_id' => auth()->user()->departamento->id,
                    'autorizado' => false,
                    'visto' => false
                ]);
            } else {

                $departamento = $this->user->departamento_id;
                $user = permisosrequisicion::getPuestoSuperiorUsuarioAutenticado($departamento);
                $autorizacion = Autorizacionhistorial::create([
                    'requisicion_id' => $requisicionNueva->id,
                    'user_id' => $user->puesto_id,
                    'user_solicita' => auth()->user()->puesto_id,
                    'departamento_id' => auth()->user()->departamento->id,
                    'autorizado' => false,
                    'visto' => false
                ]);
            }
        }





        $this->reset();

        return $requisicionNueva;
    }
}
