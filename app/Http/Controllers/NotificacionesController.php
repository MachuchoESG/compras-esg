<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Autorizacionhistorial;
use App\Models\permisosrequisicion;
use App\Models\Requisicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionesController extends Controller
{
    //
    public function getAllNotificaciones(Request $request)
    {
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

        $requisicionesPendientesAutorizarJefe = [];
        $cantidadRequisicionesPendientesAutorizarJefe = 0;
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

            if ($user->cotizacionesAutorizar() /* || $esjefe */) { //lmvilla // ESTAUS 12
                $sizeNotification = $sizeNotification + 10;
                $pendientesAutorizarCotizacion = Requisicion::getRequisicionesPendientesdeAutoriarCotizar();
                $cantidadPendienteAutorizarCotizacion = $pendientesAutorizarCotizacion->count();
                $pendienteAutorizarCotizacion = $pendientesAutorizarCotizacion;

                $totalnotificaciones = $totalnotificaciones + $cantidadPendienteAutorizarCotizacion;
            }

            //GET DE NOTIFICACIONES POR AUTORIZAR COMO JEFE DE DEPARTAMENTO
            $esJefeAutorizador = permisosrequisicion::where('PuestoAutorizador_id', $user->puesto_id)->exists();
            //return $esJefeAutorizador;
            if ($esJefeAutorizador) {
                $sizeNotification = $sizeNotification + 10;
                $PendientesAutorizarJefe = Autorizacionhistorial::where('user_id', $user->puesto_id)->where('visto', 0)->where('autorizado', 0)->pluck('requisicion_id');
                $requisicionesPendientesAutorizarJefe = Requisicion::whereIn('id', $PendientesAutorizarJefe)->where('estatus_id', 2)->select('id', 'folio')->orderBy('id', 'desc')->get();
                $totalnotificaciones = $totalnotificaciones + $requisicionesPendientesAutorizarJefe->count();
                //dd($requisicionesPendientesAutorizarJefe);
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
            'pendietesCotizacionEspecial' => empty($pendienteCotizacionEspecial) ? null : $pendienteCotizacionEspecial,//$pendienteCotizacionEspecial->count() == 0 ? null : $pendienteCotizacionEspecial,
            'pendientesaprobar' =>  $esjefe || auth()->id() == 30 ? (empty($pendientesaprobar) ? null : $pendientesaprobar) : null,
            'pendienteautorizar' => $esjefe || auth()->id() == 30 ? (empty($pendienteautorizar) ? null : $pendienteautorizar) : null,
            'pendientecotizacion' => $user->compras() ? $pendientecotizacion : null,
            'pendienteIncompletas' => empty($pendienteIncompletas) ? null : $pendienteIncompletas,
            //'totales'=> ' | ' . $cantidadPendienteAutorizarCotizacion . ' | ' . $cantidadPendienteIncompletas . ' | ' . $pendienteCotizacionEspecial->count() . ' | ',
            'pendientesAutorizarJefe' => empty($requisicionesPendientesAutorizarJefe) ? null : $requisicionesPendientesAutorizarJefe,
            'pendienteAutorizarCotizacion' => $user->cotizacionesAutorizar() ? $pendienteAutorizarCotizacion : null,
            'totalNotificaciones' => $totalnotificaciones,
            'sizeNotification' => $sizeNotification,

        ];
        return response($data, 201);
    }
}
