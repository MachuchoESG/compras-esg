<?php

namespace App\Policies;

use App\Models\Requisicion;
use App\Models\User;

class CotizacionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function autorizarCotizacion(User $user, Requisicion $requisicion)
    {
        return $user->id == 5 && $requisicion->estatus_id == 12;
    }
}
