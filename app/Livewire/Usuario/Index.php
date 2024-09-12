<?php

namespace App\Livewire\Usuario;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{




    public $open = false;
    public $userSeleccionado = [];
    public $nombre = '';
    public $search = '';




    public function accederAModal($user)
    {
        $this->userSeleccionado = $user;
        $this->nombre = $user['name'];
        $this->open = true;
    }

    public function cerrarModal()
    {
        $this->userSeleccionado = [];
    }

    public function emitirEventOpenUpdateModal($user)
    {
        //$this->emit('abrirModalEditar', $user);
        $this->dispatch('open-modal-update', $user);
        $this->userSeleccionado = $user;
    }

    public function render()
    {
        $usuarios = User::where('name', 'like', '%' . $this->search . '%')->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.usuario.index', ['usuarios' => $usuarios]);
    }
}
