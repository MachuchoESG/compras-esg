<?php

namespace App\Policies;

use App\Models\Autorizacionhistorial;
use App\Models\User;
use App\Models\Requisicion;

class RequisicionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function delete(User $user, Requisicion $requisicion)
    {
        return $user->id == $requisicion->user_id && $requisicion->estatus_id == 1;
    }

    public function autorizar(User $user, Requisicion $requisicion)
    {


        if($requisicion->estatus_id !=4 && $requisicion->estatus_id != 12){
            $autorizacion = AutorizacionHistorial::where('user_id', $user->puesto_id)
            ->where('requisicion_id', $requisicion->id)
            ->where('autorizado',0)
            ->first();

        // Verificar si se encontró una entrada y si el estatus de la requisición es válido
        return $autorizacion;
        }
        return false;
        // Verificar si el usuario tiene una entrada en la tabla autorizacionhistorial para esta requisición
       
    }

    public function aprobar(User $user, Requisicion $requisicion)
    {

        // Verificar si el usuario tiene una entrada en la tabla autorizacionhistorial para esta requisición
        $autorizacion = AutorizacionHistorial::where('user_id', $user->puesto_id)
            ->where('requisicion_id', $requisicion->id)
            ->first();

        // Verificar si se encontró una entrada y si el estatus de la requisición es válido
        return $autorizacion && $requisicion->estatus_id != 9;
    }


    public function incompleta(User $user, Requisicion $requisicion)
    {
        if ($user->departamento->name == 'Compras') {
            // Verificar que el estatus de la requisición es 7 o 10
            return $requisicion->estatus_id == 7 || $requisicion->estatus_id == 10;
        }
    
        // Si el usuario no es del departamento de compras, retorna false
        return false;
    }

    public function autorizarCotizacion(User $user, Requisicion $requisicion)
    {
        return $user->id == 30 && $requisicion->estatus_id == 12;
    }
}
