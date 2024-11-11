<?php

namespace App\Livewire\Usuario;

use App\Models\Departamento;
use App\Models\Puesto;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{




    public $open = false;
    public $userSeleccionado = [];
    public $nombre = '';
    public $search = '';

    public $departamentos = [];
    public $puestos = [];

    public $departamento_id = 0;
    public $puesto_id = 0;

    public $usuarios = [];



    public function accederAModal($user)
    {
        $this->userSeleccionado = $user;
        $this->nombre = $user['name'];
        $this->open = true;
    }

    public function cerrarModal()
    {
        $this->userSeleccionado = [];
        $this->reset('search');  // Resetear búsqueda o cualquier otra propiedad si es necesario

        // Fuerza la actualización de los datos
        $this->render();
    }

    public function emitirEventOpenUpdateModal($user)
    {
        //$this->emit('abrirModalEditar', $user);
        //$this->dispatch('open-modal-update', $user);
        $this->userSeleccionado = $user;
    }

    public function obtenerPuestos($departamentoId)
    {
        $this->departamento_id = $departamentoId;
        if($departamentoId === '0'){
            $this->puesto_id = 0;
            $this->usuarios = User::where('name', 'like', '%' . $this->search . '%')->orderBy('created_at', 'desc')->get();
        } else {
            $this->puestos = Puesto::where('departamento_id', $departamentoId)->get();
            if ($this->search !== '') {
                $this->usuarios = User::where('name', 'like', '%' . $this->search . '%')->where('departamento_id', $departamentoId)->orderBy('created_at', 'desc')->get();
            } else {
                $this->usuarios = User::where('departamento_id', $departamentoId)->orderBy('created_at', 'desc')->get();
            }
        }
        
    }

    public function filtrarUsuariosPuesto($puestoId)
    {
        if ($this->search !== '') {
            $this->usuarios = User::where('departamento_id', $this->departamento_id)->where('puesto_id', $puestoId)->where('name', 'like', '%' . $this->search . '%')->get();
        } else {
            $this->usuarios = User::where('departamento_id', $this->departamento_id)->where('puesto_id', $puestoId)->get();
        }
    }

    public function mount()
    {
        $this->departamentos = Departamento::all();
        $this->usuarios = User::where('name', 'like', '%' . $this->search . '%')->orderBy('created_at', 'desc')->get();
        //$dep1 = $this->departamentos[0];
        //$this->puestos = Puesto::where('departamento_id', '=', $dep1->id)->get();
        //dd($dep1);
    }

    public function render()
    {
        //$usuarios = User::where('name', 'like', '%' . $this->search . '%')->orderBy('created_at', 'desc')->get();
        //dd($usuarios);
        //$this->usuarios = $usuarios;
        return view('livewire.usuario.index'/* , ['usuarios' => $usuarios] */);
    }
}
