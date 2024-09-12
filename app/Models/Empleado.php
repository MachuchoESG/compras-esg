<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;
    protected $table = 'empleados';


    protected $fillable = [

        'nombre',
        'apellidos',
        'puesto_id'


    ];


    public function getNombreCompleto()
    {
        return $this->nombre . ' ' . $this->apellidos;
    }
}
