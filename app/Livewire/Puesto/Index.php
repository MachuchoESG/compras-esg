<?php

namespace App\Livewire\Puesto;

use App\Models\Departamento;
use App\Models\Puesto;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{

    use LivewireAlert;
    public $puestos = [];
    public $openEdit = false;
    public $departamentos = [];
    public $search = '';


    public $puestoEdit = [
        'id' => '',
        'name' => '',
        'departamento_id' => ''
    ];

    public function edit($id)
    {
        $puesto = Puesto::find($id);
        $this->puestoEdit['id'] = $puesto->id;
        $this->puestoEdit['name'] = $puesto->name;
        $this->puestoEdit['departamento_id'] = $puesto->departamento_id;
        $this->departamentos = Departamento::all();

        $this->openEdit = true;
    }
    
    public function update()
    {
        $this->validate([
            'puestoEdit.name' => 'required|string|max:255', // Ejemplo de reglas de validación
        ]);

        // Actualizar el nombre del departamento
        $puesto = Puesto::find($this->puestoEdit['id']);
        $puesto->name = $this->puestoEdit['name'];
        $puesto->departamento_id = $this->puestoEdit['departamento_id'];
        $puesto->save();

        // Cerrar el modal de edición
        $this->openEdit = false;

        $this->reset('puestoEdit');

        $this->alert('success', "Se actualizo correctamente el departamento " . $puesto->name);


        $this->puestos = Puesto::with('departamento')->get();
    }

    #[On('puesto-created')]
    public function updateDepartamentos()
    {
        $this->puestos = Puesto::with('departamento')->get();
    }
    public function mount()
    {

        $this->puestos = Puesto::with('departamento')->get();
    }
    public function render()
    {


        $this->puestos = Puesto::where('name', 'like', '%' . $this->search . '%')->get();

        return view('livewire.puesto.index');
    }
}
