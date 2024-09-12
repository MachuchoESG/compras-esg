<?php

namespace App\Livewire\Permisosrequisicion;

use App\Models\Departamento;
use App\Models\permisosrequisicion;
use App\Models\Puesto;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Index extends Component
{
    use LivewireAlert;
    public $openEdit = false;
    public $puestos = [];
    public $departamentos = [];
    public $permisos = [];
    public $search = '';

    #[Rule([
        'permisoEdit.PuestoSolicitante_id' => 'required',
        'permisoEdit.PuestoAutorizador_id' => 'required',
        'permisoEdit.Departamento_id' => 'required',
        'permisoEdit.monto' => 'required'


    ], [], [
        'permisoEdit.PuestoSolicitante_id' => 'Puesto Solicitante',
        'permisoEdit.PuestoAutorizador_id' => 'Puesto Autorización',
        'permisoEdit.Departamento_id' => 'Departamento',
        'permisoEdit.monto' => 'Monto'
    ])]

    public $permisoEdit = [
        'id' => '',
        'PuestoSolicitante_id' => '',
        'PuestoAutorizador_id' => '',
        'Departamento_id' => '',
        'monto' => '',
    ];

    public function edit($id)
    {
        $flujoautorizacion = permisosrequisicion::find($id);
        $this->permisoEdit['id'] = $flujoautorizacion->id;
        $this->permisoEdit['PuestoSolicitante_id'] = $flujoautorizacion->PuestoSolicitante_id;
        $this->permisoEdit['PuestoAutorizador_id'] = $flujoautorizacion->PuestoAutorizador_id;
        $this->permisoEdit['Departamento_id'] = $flujoautorizacion->Departamento_id;
        $this->permisoEdit['monto'] = $flujoautorizacion->monto;
        $this->openEdit = true;
    }
    public function update()
    {
        $this->validate();



        // Encuentra el permiso a actualizar
        $flujoautorizacion = permisosrequisicion::find($this->permisoEdit['id']);

        // Actualiza los valores
        $flujoautorizacion->update([
            'PuestoSolicitante_id' => $this->permisoEdit['PuestoSolicitante_id'],
            'PuestoAutorizador_id' => $this->permisoEdit['PuestoAutorizador_id'],
            'Departamento_id' => $this->permisoEdit['Departamento_id'],
            'monto' => $this->permisoEdit['monto']
        ]);

        $this->reset('permisoEdit');
        $this->openEdit = false;

        $this->alert('success', "Se actualizo correctamente la informacion");

        $this->puestos = Puesto::all();
        $this->departamentos = Departamento::all();
        $this->permisos = permisosrequisicion::with('puestosolicitante', 'puestoautorizador', 'departamento')->get();
    }

    #[On('puesto-created')]
    public function updateDepartamentos()
    {
        $this->puestos = Puesto::all();
        $this->departamentos = Departamento::all();
        $this->permisos = permisosrequisicion::with('puestosolicitante', 'puestoautorizador', 'departamento')->get();
    }
    public function mount()
    {

        $this->puestos = Puesto::all();
        $this->departamentos = Departamento::all();
        $this->permisos = permisosrequisicion::with('puestosolicitante', 'puestoautorizador', 'departamento')->get();
    }
    public function placeholder()
    {
        return view('livewire.placeholder.loading');
    }
    public function render()
    {
        $busqueda = $this->search;

        $this->permisos = PermisosRequisicion::with(['puestoSolicitante', 'puestoAutorizador', 'departamento'])
            ->whereHas('puestoSolicitante', function ($query) use ($busqueda) {
                $query->where('name', 'like', '%' . $busqueda . '%');
            })
            ->orWhereHas('puestoAutorizador', function ($query) use ($busqueda) {
                $query->where('name', 'like', '%' . $busqueda . '%');
            })
            ->get();

        return view('livewire.permisosrequisicion.index');
    }
}
