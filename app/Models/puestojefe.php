<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class puestojefe extends Model
{
    use HasFactory;


    protected $table = 'puestojefe';

    public static function getPuestosEmpleadosBajoSupervision()
    {
        // Obtén el puesto_id del usuario autenticado
        $puestoIdUsuario = Auth::user()->puesto_id;

        // Verifica si el usuario es un jefe en la tabla puestojefe
        $esJefe = self::where('jefe_id', $puestoIdUsuario)->exists();

        // Si el usuario es un jefe, obtén la lista de puestos de los empleados bajo su supervisión
        if ($esJefe) {
            return self::where('jefe_id', $puestoIdUsuario)
                ->whereNotNull('puesto_id')
                ->pluck('puesto_id');
        } else {
            // Si el usuario no es un jefe, devolvemos una colección vacía
            return collect();
        }
    }
}
