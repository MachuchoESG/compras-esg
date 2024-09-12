<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        // otros campos en el modelo
    ];

    public function puestos()
    {
        return $this->hasMany(Puesto::class);
    }
}
