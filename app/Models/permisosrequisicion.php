<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class permisosrequisicion extends Model
{
    use HasFactory;
    protected $table = 'permisosautorizacionrequisiciones';
    protected $fillable = [
        'PuestoSolicitante_id',
        'PuestoAutorizador_id',
        'Departamento_id',
        'monto'
        // otros campos en el modelo
    ];

    public function puestosolicitante()
    {
        return $this->belongsTo(Puesto::class, 'PuestoSolicitante_id');
    }

    public function puestoautorizador()
    {
        return $this->belongsTo(Puesto::class, 'PuestoAutorizador_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'Departamento_id');
    }

    public static function getPuestosEmpleadosBajoSupervision()
    {
        $puestoIdUsuario = Auth::user()->puesto_id;

        $esJefe = self::where('PuestoAutorizador_id', $puestoIdUsuario)->exists();

        if ($esJefe) {
            return self::where('PuestoAutorizador_id', $puestoIdUsuario)
                ->whereNotNull('PuestoSolicitante_id')
                ->pluck('PuestoSolicitante_id');
        } else {
            return collect();
        }
    }

    /**
     * Obtiene el puesto superior del usuario autenticado en el mismo departamento.
     */
    public static function getPuestoSuperiorUsuarioAutenticado($departamento_id)
    {
        $usuario = Auth::user();
        $puestoIdUsuario = $usuario->puesto_id;
        // $departamento_id = $usuario->departamento->id;

        $puestoAutorizadorId = self::where('PuestoSolicitante_id', $puestoIdUsuario)
            ->where('departamento_id', $departamento_id)
            ->value('PuestoAutorizador_id');

        if ($puestoAutorizadorId) {
            return User::where('puesto_id', $puestoAutorizadorId)->first();
        }

        return null;
    }



    // public static function getPuestosEmpleadosBajoSupervision()
    // {
    //     // Obtén el puesto_id del usuario autenticado
    //     $puestoIdUsuario = Auth::user()->puesto_id;

    //     // Verifica si el usuario es un jefe en la tabla puestojefe
    //     $esJefe = self::where('PuestoAutorizador_id', $puestoIdUsuario)->exists();

    //     // Si el usuario es un jefe, obtén la lista de puestos de los empleados bajo su supervisión
    //     if ($esJefe) {
    //         return self::where('PuestoAutorizador_id', $puestoIdUsuario)
    //             ->whereNotNull('PuestoSolicitante_id')
    //             ->pluck('PuestoSolicitante_id');
    //     } else {
    //         // Si el usuario no es un jefe, devolvemos una colección vacía
    //         return collect();
    //     }
    // }

    // public static function getPuestoSuperiorUsuarioAutenticado()
    // {
    //     // Obtén el departamento_id del usuario autenticado
    //     $usuario = Auth::user();


    //     $puestoIdUsuario = Auth::user()->puesto_id;

    //     $departamento_id = $usuario->departamento->id;

    //     // Busca el puesto del superior del usuario autenticado en el mismo departamento
    //     $puestoAutorizadorId = self::where('PuestoSolicitante_id', $puestoIdUsuario)
    //         ->where('departamento_id', $departamento_id)
    //         ->value('PuestoAutorizador_id');

    //     if ($puestoAutorizadorId) {
    //         $usuarioPuestoAutorizador = User::where('puesto_id', $puestoAutorizadorId)->first();
    //         return $usuarioPuestoAutorizador;
    //     }

    //     // Si no se encuentra ningún usuario asociado al puesto autorizador, devuelve null
    //     return null;
    // }
}
