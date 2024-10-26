<?php

namespace App\Livewire\Notification;

use App\Models\Requisicion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notification extends Component
{
    public $cantidadPendienteAprobar = 0;
    public $pendientesaprobar = [];

    public $cantidadPendienteAutorizar = 0;
    public $pendienteautorizar = [];


    public $cantidadPendienteCotizacion = 0;
    public $pendientecotizacion = [];


    public $cantidadPendienteIncompletas = 0;
    public $pendienteIncompletas = [];

    public $cantidadPendienteAutorizarCotizacion = 0;
    public $pendienteAutorizarCotizacion = [];


    public $totalnotificaciones = 0;

    public $esjefe = false;
    public $escompras = false;

    public $sizeNotification = 10; //no toma query sizes se usa backend para modificar tamaño

    public function mount()
    {
        $this->cargarDepartamento();
    }

    public function cargarDepartamento()
    {
        $user = Auth::user(); // Obtener el usuario autenticado

        if ($user) {
            $this->escompras = $user->compras();

            if ($this->escompras) { //lmvilla //ESTATUS 7
                $this->sizeNotification = $this->sizeNotification + 10;
                $requisicionesPendientes = Requisicion::getRequisicionesPendientesdeCotizar();

                $this->cantidadPendienteCotizacion = $requisicionesPendientes->count();
                $this->pendientecotizacion = $requisicionesPendientes;
                $this->totalnotificaciones = $this->cantidadPendienteCotizacion;
            }

            $this->esjefe = $user->jefe();

            if ($this->esjefe || auth()->id() == 30) {

                $this->sizeNotification = $this->sizeNotification + 10;
                $requisionesPendientesAprobar = Requisicion::getRequisicionesPendientesAprobar();
                if ($requisionesPendientesAprobar === 0) {
                    $this->cantidadPendienteAprobar = 0;
                } else {
                    $this->cantidadPendienteAprobar = $requisionesPendientesAprobar->count();
                }

                $this->pendientesaprobar = $requisionesPendientesAprobar;

                $requisionesPendientesAutorizar = Requisicion::getRequisicionesPendientesdeAutorizar();
                if ($requisionesPendientesAutorizar === 0) {
                    $this->cantidadPendienteAutorizar = 0;
                } else {
                    $this->cantidadPendienteAutorizar = $requisionesPendientesAutorizar->count();
                }

                $this->pendienteautorizar = $requisionesPendientesAutorizar;

                $this->totalnotificaciones = $this->totalnotificaciones + $this->cantidadPendienteAprobar + $this->cantidadPendienteAutorizar;
            }

            if ($user->cotizacionesAutorizar()) { //lmvilla // ESTAUS 12
                $this->sizeNotification = $this->sizeNotification + 10;
                $pendientesAutorizarCotizacion = Requisicion::getRequisicionesPendientesdeAutoriarCotizar();
                $this->cantidadPendienteAutorizarCotizacion = $pendientesAutorizarCotizacion->count();
                $this->pendienteAutorizarCotizacion = $pendientesAutorizarCotizacion;


                $this->totalnotificaciones = $this->totalnotificaciones + $this->cantidadPendienteAutorizarCotizacion;
            }

            if ($this->sizeNotification === 40) {
                $this->sizeNotification = 50;
            }

            $requisicionesIncompletas = Requisicion::getRequisicionesIncompletas(); // ESTATUS 10
            $this->cantidadPendienteIncompletas = $requisicionesIncompletas->count();
            $this->pendienteIncompletas = $requisicionesIncompletas;
            $this->totalnotificaciones = $this->totalnotificaciones + $this->cantidadPendienteIncompletas;

        }
    }


    public function renderNotificationsData(){
        dd('hola');
    }

    public function render()
    {
        return view('livewire.notification.notification');
    }
}
