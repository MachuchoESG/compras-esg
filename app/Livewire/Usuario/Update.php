<?php

namespace App\Livewire\Usuario;

use App\Models\Departamento;
use App\Models\Puesto;
use App\Models\User;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use LivewireAlert;

    public $userSelected;
    public $openMUpdateUser = false;
    public $lista_departamentos = [];
    public $lista_puestos = [];
    public $name;
    public $email;
    public $departamento_id;
    public $puesto_id;

    protected  $listeners = ['abrirModalEditar'];


    public function abrirModalEditar($user)
    {
        $this->userSelected = $user;
        $this->openMUpdateUser = true;
        $this->name = $user['name'];
        $this->email = $user['email'];
        $this->departamento_id = $user['departamento_id'];
        $this->puesto_id = $user['puesto_id'];
        $this->getOptionesUsersForm($user['departamento_id']);
        //$this->render();
    }

    public function getOptionesUsersForm($departamento_id)
    {
        $departamentos = Departamento::all();
        $this->lista_departamentos = $departamentos;

        $puestos = Puesto::Where('departamento_id', $departamento_id)->get();
        $this->lista_puestos = $puestos;
    }

    public function obtenerPuestos($departamento_id)
    {
        $puestos = Puesto::Where('departamento_id', $departamento_id)->get();
        $this->lista_puestos = $puestos;
    }

    public function cerrarModal()
    {
        // Reiniciar valores cuando se cierra el modal
        $this->openMUpdateUser = false;
        $this->clearInputs();
        $this->dispatch('modal-update-closed');
    }

    public function clearInputs()
    {
        $this->name = '';
        $this->email = '';
        $this->departamento_id = null;
        $this->puesto_id = null;
        $this->lista_departamentos = [];
        $this->lista_puestos = [];
    }

    public function mount($userSeleccionado)
    {
        $this->dispatch('modal-update-init');
        $this->abrirModalEditar($userSeleccionado);
    }

    public function save()
    {

        $rules = [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'departamento_id' => ['required'],
            'puesto_id' => ['required']
        ];


        // Actualizar el nombre del departamento
        $usuario = User::find($this->userSelected['id']); //Puesto::find($this->puestoEdit['id']);
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'departamento_id' => $this->departamento_id,
            'puesto_id' => $this->puesto_id
        ];
        //dd($usuario);
        $usuario->name = $this->name;
        $usuario->email = $this->email;
        $usuario->departamento_id = $this->departamento_id;
        $usuario->puesto_id = $this->puesto_id;

        $validator = FacadesValidator::make($data, $rules);

        // Validar los datos
        if ($validator->fails()) {
            $errors = $validator->errors();
            $arrErrors = $errors->toArray();
            //dd($arrErrors);

            foreach ($arrErrors as $errors) {
                $errMessage = '';
                foreach ($errors as $message) {
                    $errMessage = $errMessage . $message;
                }
                $this->alert('error', "Error al actualizar: " . $message, ['timer' => '5000']);
            }

            //$this->alert('error', "Error al actualizar: " . $errors, ['timer' => '5000']);
            return;
        }

        //dd($usuario);
        $usuario->save();
        $this->cerrarModal();
        $this->alert('success', "Se actualizo correctamente el departamento " . $usuario->name);
    }

    public function render()
    {
        return view('livewire.usuario.update');
    }
}
