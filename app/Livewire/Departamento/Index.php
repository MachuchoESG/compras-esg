<?php

namespace App\Livewire\Departamento;

use App\Models\Departamento;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{

    use LivewireAlert;


    public $search = '';


    public $openEdit = false;
    public $departamentoEdit = [
        'id' => '',
        'name' => ''
    ];

    public function edit($id)
    {
        $selectDep = Departamento::find($id);
        $this->departamentoEdit['id'] = $selectDep->id;
        $this->departamentoEdit['name'] = $selectDep->name;
        $this->openEdit = true;
    }
    public function update()
    {
        $this->validate([
            'departamentoEdit.name' => 'required|string|max:255', // Ejemplo de reglas de validación
        ]);

        // Actualizar el nombre del departamento
        $departamento = Departamento::find($this->departamentoEdit['id']);
        $departamento->name = $this->departamentoEdit['name'];
        $departamento->save();

        // Cerrar el modal de edición
        $this->openEdit = false;

        $this->reset('departamentoEdit');

        $this->alert('success', "Se actualizo correctamente el departamento " . $departamento->name);
    }

    #[On('departamento-created')]
    public function updateDepartamentos() {}



    public function render()
    {

        $departamentos = Departamento::where('name', 'like', '%' . $this->search . '%')->paginate(10);

        return view('livewire.departamento.index', ['departamentos' => $departamentos]);
    }
}
