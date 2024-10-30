<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Requisicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionesController extends Controller
{
    //
    public function getAllNotificaciones(Request $request){
        $cantidadPendienteAprobar = 0;
        $pendientesaprobar = [];
    
        $cantidadPendienteAutorizar = 0;
        $pendienteautorizar = [];
    
    
        $cantidadPendienteCotizacion = 0;
        $pendientecotizacion = [];
    
    
        $cantidadPendienteIncompletas = 0;
        $pendienteIncompletas = [];
    
        $cantidadPendienteAutorizarCotizacion = 0;
        $pendienteAutorizarCotizacion = [];
        
        $cantidadPendienteCotizacionEspecial = 0;
        $pendienteCotizacionEspecial = [];
    
        $totalnotificaciones = 0;
    
        $esjefe = false;
        $escompras = false;
    
        $sizeNotification = 10;

        $user = Auth::user(); // Obtener el usuario autenticado

        if ($user) {
            $escompras = $user->compras();

            if ($escompras) { //lmvilla //ESTATUS 7
                $sizeNotification = $sizeNotification + 10;
                $requisicionesPendientes = Requisicion::getRequisicionesPendientesdeCotizar();

                $cantidadPendienteCotizacion = $requisicionesPendientes->count();
                $pendientecotizacion = $requisicionesPendientes;
                $totalnotificaciones = $cantidadPendienteCotizacion;
            }

            $esjefe = $user->jefe();

            if ($esjefe || auth()->id() == 30) {

                $sizeNotification = $sizeNotification + 10;
                $requisionesPendientesAprobar = Requisicion::getRequisicionesPendientesAprobar();
                if ($requisionesPendientesAprobar === 0) {
                    $cantidadPendienteAprobar = 0;
                } else {
                    $cantidadPendienteAprobar = $requisionesPendientesAprobar->count();
                }

                $pendientesaprobar = $requisionesPendientesAprobar;

                $requisionesPendientesAutorizar = Requisicion::getRequisicionesPendientesdeAutorizar();
                if ($requisionesPendientesAutorizar === 0) {
                    $cantidadPendienteAutorizar = 0;
                } else {
                    $cantidadPendienteAutorizar = $requisionesPendientesAutorizar->count();
                }

                $pendienteautorizar = $requisionesPendientesAutorizar;

                $totalnotificaciones = $totalnotificaciones + $cantidadPendienteAprobar + $cantidadPendienteAutorizar;
            }

            if ($user->cotizacionesAutorizar()) { //lmvilla // ESTAUS 12
                $sizeNotification = $sizeNotification + 10;
                $pendientesAutorizarCotizacion = Requisicion::getRequisicionesPendientesdeAutoriarCotizar();
                $cantidadPendienteAutorizarCotizacion = $pendientesAutorizarCotizacion->count();
                $pendienteAutorizarCotizacion = $pendientesAutorizarCotizacion;


                $totalnotificaciones = $totalnotificaciones + $cantidadPendienteAutorizarCotizacion;
            }

            if ($sizeNotification === 40) {
                $sizeNotification = 50;
            }

            $pendienteCotizacionEspecial = Requisicion::getRequisicionesEspecialesNotifys();
            $requisicionesIncompletas = Requisicion::getRequisicionesIncompletas(); // ESTATUS 10
            $cantidadPendienteIncompletas = $requisicionesIncompletas->count();
            $pendienteIncompletas = $requisicionesIncompletas;
            $totalnotificaciones = $totalnotificaciones + $cantidadPendienteIncompletas + $pendienteCotizacionEspecial->count();

        }
        
        $data = [
            'pendietesCotizacionEspecial' => $pendienteCotizacionEspecial,
            'pendientesaprobar'=>  $esjefe || auth()->id() == 30 ? $pendientesaprobar : null, 
            'pendienteautorizar' => $esjefe || auth()->id() == 30 ? $pendienteautorizar : null,
            'pendientecotizacion' => $user->compras() ? $pendientecotizacion : null,
            'pendienteIncompletas' => $pendienteIncompletas,
            'pendienteAutorizarCotizacion' => $user->cotizacionesAutorizar() ? $pendienteAutorizarCotizacion : null,
            'totalNotificaciones' => $totalnotificaciones,
            'sizeNotification' => $sizeNotification,
            
        ];
        return response($data, 201);
    }
}
