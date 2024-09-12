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
    public $pendienteIncompletas= [];

    public $cantidadPendienteAutorizarCotizacion= 0;
    public $pendienteAutorizarCotizacion= [];


    public $totalnotificaciones = 0;

    public $esjefe = false;
    public $escompras = false;



    public function mount()
    {
        $this->cargarDepartamento();
    }

    public function cargarDepartamento()
    {
        $user = Auth::user(); // Obtener el usuario autenticado

        if ($user) {
            $this->escompras = $user->compras();

            if ($this->escompras) {


             
                $requisicionesPendientes = Requisicion::getRequisicionesPendientesdeCotizar();
                $this->cantidadPendienteCotizacion = $requisicionesPendientes->count();
                $this->pendientecotizacion = $requisicionesPendientes;
                $this->totalnotificaciones = $this->cantidadPendienteCotizacion;

              

            }

            $this->esjefe = $user->jefe();

            if ($this->esjefe) {
                //$this->cantidadPendienteAprobar = Requisicion::contarRequisicionesPendientesdeAprobar();
                $requisionesPendientesAprobar = Requisicion::getRequisicionesPendientesAprobar();
                $this->cantidadPendienteAprobar = $requisionesPendientesAprobar->count();
                $this->pendientesaprobar = $requisionesPendientesAprobar;



                $requisionesPendientesAutorizar = Requisicion::getRequisicionesPendientesdeAutorizar();
                $this->cantidadPendienteAutorizar = $requisionesPendientesAutorizar->count();
                $this->pendienteautorizar = $requisionesPendientesAutorizar;

                $this->totalnotificaciones = $this->cantidadPendienteAprobar + $this->cantidadPendienteAutorizar;
            }

            if($user->cotizacionesAutorizar()){
                $pendientesAutorizarCotizacion = Requisicion::getRequisicionesPendientesdeAutoriarCotizar();
                $this->cantidadPendienteAutorizarCotizacion = $pendientesAutorizarCotizacion->count();
                $this->pendienteAutorizarCotizacion = $pendientesAutorizarCotizacion;


                $this->totalnotificaciones = $this->totalnotificaciones + $this->cantidadPendienteAutorizarCotizacion;
            }


            $requisicionesIncompletas =Requisicion::getRequisicionesIncompletas();
            $this->cantidadPendienteIncompletas = $requisicionesIncompletas->count();
            $this->pendienteIncompletas = $requisicionesIncompletas;
            $this->totalnotificaciones = $this->totalnotificaciones + $this->cantidadPendienteIncompletas;
            ;


        }
    }


    public function render()
    {
        return view('livewire.notification.notification');
    }
}
