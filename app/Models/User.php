<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'departamento_id',
        'puesto_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function getNombreCompleto()
    {
        return $this->name;
    }

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'user_empresa_sucursal', 'user_id', 'empresa_id');
    }

    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'user_empresa_sucursal', 'user_id', 'sucursal_id');
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

    
    public static function cotizacionesAutorizar()
    {

        $user_id = Auth::user()->id;
        if ($user_id == 30) {
            return true;
        }
        return false;
    }


    public function getPuestosEmpleadosBajoSupervision()
    {
        $puestoIdUsuario = $this->puesto_id;

        $esJefe = permisosrequisicion::where('PuestoAutorizador_id', $puestoIdUsuario)->exists();

        if ($esJefe) {
            return permisosrequisicion::where('PuestoAutorizador_id', $puestoIdUsuario)
                ->whereNotNull('PuestoSolicitante_id')
                ->pluck('PuestoSolicitante_id');
        } else {
            return collect();
        }
    }

    public function getPuestoSuperior()
    {
        $puestoIdUsuario = $this->puesto_id;
        $departamentoId = $this->departamento->id;

        $puestoAutorizadorId = permisosrequisicion::where('PuestoSolicitante_id', $puestoIdUsuario)
            ->where('departamento_id', $departamentoId)
            ->value('PuestoAutorizador_id');

        if ($puestoAutorizadorId) {
            return self::where('puesto_id', $puestoAutorizadorId)->first();
        }

        return null;
    }
}
