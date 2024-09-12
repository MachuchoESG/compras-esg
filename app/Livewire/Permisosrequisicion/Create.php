<?php

namespace App\Livewire\Permisosrequisicion;

use App\Models\Departamento;
use App\Models\permisosrequisicion;
use App\Models\Puesto;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{

    use LivewireAlert;
    public $open = false;
    public $puestos = [];
    public $departamentos = [];


    #[Rule([
        'permiso.PuestoSolicitante_id' => 'required',
        'permiso.PuestoAutorizador_id' => 'required',
        'permiso.Departamento_id' => 'required',
        'permiso.monto' => 'required'


    ], [], [
        'permiso.PuestoSolicitante_id' => 'Puesto Solicitante',
        'permiso.PuestoAutorizador_id' => 'Puesto Autorización',
        'permiso.Departamento_id' => 'Departamento',
        'permiso.monto' => 'Monto'
    ])]

    public $permiso = [
        'PuestoSolicitante_id' => '',
        'PuestoAutorizador_id' => '',
        'Departamento_id' => '',
        'monto' => '',
    ];


    protected function rules()
    {
        return [
            'permiso.PuestoSolicitante_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existingRecord = permisosrequisicion::where('PuestoSolicitante_id', $this->permiso['PuestoSolicitante_id'])
                        ->where('PuestoAutorizador_id', $this->permiso['PuestoAutorizador_id'])
                        ->where('Departamento_id', $this->permiso['Departamento_id'])
                        ->first();

                    if ($existingRecord) {
                        $fail('El flujo de autorización ya existe para este conjunto de valores.');
                    }
                }
            ],
            'permiso.PuestoAutorizador_id' => 'required',
            'permiso.Departamento_id' => 'required',
            'permiso.monto' => 'required|numeric|min:0.01'
        ];
    }
    public function messages()
    {
        return [
            'permiso.PuestoSolicitante_id.required' => 'El campo solicitante es obligatorio.',
            'permiso.PuestoAutorizador_id.required' => 'El campo autorizador es obligatorio.',
            'permiso.Departamento_id.required' => 'El campo departamento es obligatorio.',
            'permiso.monto.required' => 'El campo monto es obligatorio.',
            'permiso.monto.numeric' => 'El campo monto debe ser numérico.',
            'permiso.monto.min' => 'El campo monto debe ser mayor que :min.',
        ];
    }

    public function mount()
    {
        $this->departamentos = Departamento::all();
        $this->puestos = Puesto::all();
    }

    public function save()
    {

        $this->validate();

        $permiso = permisosrequisicion::create($this->permiso);

        $this->alert('success', "Se guardó correctamente la información");

        return redirect()->route('permisosrequisicion.index');
    }

    

    public function render()
    {
        return view('livewire.permisosrequisicion.create');
    }
}
