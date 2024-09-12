<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;
    protected $table = 'sucursales';


    protected $fillable = [
        'name',
        'nomenclatura',
        'empresa_id',
        // otros campos en el modelo
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
