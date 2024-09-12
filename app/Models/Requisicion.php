<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Requisicion extends Model
{
    use HasFactory;
    protected $table = 'requisiciones';

    protected $fillable = [
        'folio',
        'proveedor',
        'observaciones',
        'estatus_id',
        'ordenCompra',
        'empleado_id',
        'sucursal_id',
        'user_id',
        'empresa_id',
        'fecharequerida',
        'seguimiento',
        'aprobado',
        'visto',
        'unidad',
        'proyecto_id',
        'proyecto'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($requisition) {
            // Utilizar una transacción para asegurar la consistencia
            DB::transaction(function () use ($requisition) {
                // Obtener la nomenclatura de la sucursal seleccionada
                $nomenclatura = Sucursal::find($requisition->sucursal_id)->nomenclatura;

                // Obtener el último folio de la sucursal dentro de la transacción
                $lastFolio = self::where('sucursal_id', $requisition->sucursal_id)
                    ->orderBy('id', 'desc')
                    ->value('folio');

                // Extraer el número del último folio
                if ($lastFolio) {
                    $lastNumber = (int) substr($lastFolio, strrpos($lastFolio, '-') + 1);
                } else {
                    $lastNumber = 1;
                }

                // Crear el nuevo folio con la nomenclatura y el número
                $newNumber = $lastNumber + 1;
                $folio = $nomenclatura . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

                // Asignar el folio al modelo
                $requisition->folio = $folio;

                // Resto de la lógica de creación de la requisición
            });
        });
    }

    // Definir la relación con la sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    public function estatus()
    {
        return $this->belongsTo(Estatus::class);
    }

    public function detalleRequisiciones()
    {
        return $this->hasMany(DetalleRequisicion::class);
    }


    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class);
    }


    public function evidencia()
    {
        return $this->hasMany(Evidencia::class);
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    public function historialesAutorizacion()
    {
        return $this->hasMany(Autorizacionhistorial::class, 'requisicion_id');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentarios::class, 'requisicion_id')->orderBy('created_at', 'desc');
    }


    public static function getRequisicionesIncompletas()
    {

        return Requisicion::where('estatus_id', 10)
            ->where('user_id', Auth::id())
            ->select('id', 'folio')
            ->get();
    }

    public static function getRequisiciones($search, $paginate)
    {
        $user = Auth::user();

        if (self::vertodaslasrequisicones()) {
            return self::with('estatus', 'solicitante')
                ->where('folio', 'like', '%' . $search  . '%')
                ->orWhere('proveedor', 'like', '%' . $search . '%')
                ->orWhere('ordenCompra', 'like', '%' . $search . '%')
                ->where('estatus_id', '!=', 9)
                ->orderBy('created_at', 'desc')
                ->paginate($paginate);
        } else {
            if (self::jefe()) {
                $requisicionesIds = Autorizacionhistorial::where('user_id', $user->puesto->id)
                    ->pluck('requisicion_id');

                return self::with('estatus', 'solicitante')
                    ->whereIn('id', $requisicionesIds)

                    ->where('estatus_id', '!=', 9)
                    ->where('folio', 'like', '%' . $search  . '%')
                    ->orderBy('created_at', 'desc')
                    ->paginate($paginate);
            } else {
                if (self::compras()) {
                    return self::with('estatus', 'solicitante')
                        ->where('aprobado', 1)
                        ->where('folio', 'like', '%' . $search  . '%')
                        ->where('estatus_id', '!=', 9)
                        ->orderBy('created_at', 'desc')
                        ->paginate($paginate);
                } else {


                    return self::with('estatus', 'solicitante')
                        ->where('user_id', $user->id)
                        ->where('estatus_id', '!=', 9)
                        ->where('folio', 'like', '%' . $search  . '%')
                        ->orderBy('created_at', 'desc')
                        ->paginate($paginate);
                }
            }
        }
    }
    public static function getRequisicionesPendientesdeAutorizar()
    {
        // Obtener el usuario autenticado

        $puestoIdUsuario = Auth::user()->puesto_id;
        $USER_ID = Auth::user()->id;
        $esJefe = permisosrequisicion::where('PuestoAutorizador_id', $puestoIdUsuario)->exists();
        // Verificar si el usuario es un jefe
        if ($esJefe) {
            // Obtén el recuento de requisiciones no vistas
            return Requisicion::whereIn('id', function ($query) use ($puestoIdUsuario) {
                $query->select('requisicion_id')
                    ->from('autorizacionhistorial')
                    ->where('user_id', $puestoIdUsuario)
                    ->where('visto', 0); //antes estaba visto
            })
                ->where('aprobado', 1)
                ->where('estatus_id', 2)

                ->select('id', 'folio')
                ->get();

            // return Requisicion::whereNotIn('id', function ($query) use ($USER_ID) {
            //     $query->select('requisicion_id')
            //         ->from('autorizacionhistorial')
            //         ->where('user_id', $USER_ID)
            //         ->where('visto', 0);
            // })
            //     ->where(function ($query) {
            //         $query->where('aprobado', 1)
            //             ->where('estatus_id', 2);
            //     })
            //     ->orWhere(function ($query) use ($USER_ID) {
            //         $query->where('user_id', $USER_ID)
            //             ->where('estatus_id', 2);
            //     })
            //     ->select('id', 'folio')
            //     ->get();
        } else {
            // Si el usuario no es un jefe, devolvemos 0 requisiciones sin ver pendientes
            return 0;
        }
    }


    public static function getRequisicionesPendientesAprobar()
    {
        // Obtener el usuario autenticado

        $puestoIdUsuario = Auth::user()->puesto_id;
        $USER_ID = Auth::user()->id;
        $esJefe = permisosrequisicion::where('PuestoAutorizador_id', $puestoIdUsuario)->exists();
        // Verificar si el usuario es un jefe
        if ($esJefe) {

            return Requisicion::whereIn('empleado_id', function ($query) use ($puestoIdUsuario) {
                $query->select('id')
                    ->from('users')
                    ->whereIn('puesto_id', function ($subQuery) use ($puestoIdUsuario) {
                        $subQuery->select('PuestoSolicitante_id')
                            ->from('permisosautorizacionrequisiciones')
                            ->where('puestoautorizador_id', $puestoIdUsuario);
                    });
            })
                ->where('Visto', 0)
                ->where('estatus_id', 1)
                ->select('id', 'folio')
                ->get();

            // return Requisicion::whereIn('empleado_id', function ($query) use ($puestoIdUsuario) {
            //     $query->select('PuestoSolicitante_id')
            //         ->from('permisosautorizacionrequisiciones')
            //         ->where('puestoautorizador_id', $puestoIdUsuario);
            // })
            //     ->where('Visto', 0)
            //     ->select('id', 'folio')
            //     ->get();
        } else {
            // Si el usuario no es un jefe, devolvemos 0 requisiciones sin ver pendientes
            return 0;
        }
    }

    public static function jefe()
    {
        // Obtener los puestos de empleados bajo supervisión del usuario autenticado
        $puestosEmpleadosBajoSupervision = permisosrequisicion::getPuestosEmpleadosBajoSupervision();

        // Verificar si el usuario autenticado es un jefe
        return $puestosEmpleadosBajoSupervision->isNotEmpty();
    }

    public static function compras()
    {

        $departamento_Id = Auth::user()->departamento_id;
        if ($departamento_Id == 2) {
            return true;
        }
        return false;
    }

    public static function vertodaslasrequisicones()
    {
        return Auth::user()->departamento_id == 2 || Auth::user()->departamento_id ==  6 || Auth::user()->id == 5 || Auth::user()->id == 28; // PRODUCCION
    }

    public static function contarRequisicionesPendientesdeAprobar()
    {
        // Obtén el puesto_id del usuario autenticado
        $puestoIdUsuario = Auth::user()->puesto_id;
        $user_id = Auth::user()->id;

        // Verifica si el usuario es un jefe en la tabla puestojefe
        $esJefe = permisosrequisicion::where('PuestoAutorizador_id', $puestoIdUsuario)->exists();

        // Si el usuario es un jefe, contar las requisiciones sin ver pendientes
        if ($esJefe) {
            // Obtén el recuento de requisiciones no vistas
            return Requisicion::whereIn('empleado_id', function ($query) use ($puestoIdUsuario) {
                $query->select('PuestoSolicitante_id')
                    ->from('permisosautorizacionrequisiciones')
                    ->where('puestoautorizador_id', $puestoIdUsuario);
            })
                ->where('Visto', 0)
                ->count();
        } else {
            // Si el usuario no es un jefe, devolvemos 0 requisiciones sin ver pendientes
            return 0;
        }
    }

    public static function contarRequisicionesPendientesdeCotizacion()
    {

        return Requisicion::where('estatus_id', 1)
            ->where('aprobado', 1)
            ->where('visto', 1) // Filtrar por requisiciones no vistas
            ->count();
    }
    public static function  getRequisicionesPendientesdeCotizar()
    {



        if (Auth::user()->id == 30) {
            return collect();
        }
        return Requisicion::with('solicitante')->where('estatus_id', 7)->orderBy('updated_at', 'desc')
            ->get();
    }

    public static function contarRequisicionesPendientesdeAutorizar()
    {
        // Obtén el puesto_id del usuario autenticado
        $puestoIdUsuario = Auth::user()->puesto_id;
        $user_id = Auth::user()->id;

        // Verifica si el usuario es un jefe en la tabla puestojefe
        $esJefe = permisosrequisicion::where('PuestoAutorizador_id', $puestoIdUsuario)->exists();

        // Si el usuario es un jefe, contar las requisiciones sin ver pendientes
        if ($esJefe) {
            // Obtén el recuento de requisiciones no vistas
            return Requisicion::whereIn('id', function ($query) use ($user_id) {
                $query->select('requisicion_id')
                    ->from('autorizacionhistorial')
                    ->where('user_id', $user_id)
                    ->where('visto', 0);
            })
                ->where('aprobado', 1)
                ->where('estatus_id', 2)
                ->count(); // Usamos count() en lugar de get()
        } else {
            // Si el usuario no es un jefe, devolvemos 0 requisiciones sin ver pendientes
            return 0;
        }
    }

    public static function  getRequisicionesPendientesdeAutoriarCotizar()
    {

        return Requisicion::where('estatus_id', 12)
            ->get();
    }
}
