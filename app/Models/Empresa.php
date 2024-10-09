<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        // otros campos en el modelo
    ];

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }

    public function gastosFijos()
    {
        return $this->hasMany(GastoFijo::class, 'id_empresa');
    }
}
