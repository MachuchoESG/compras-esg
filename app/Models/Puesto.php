<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'departamento_id'
        // otros campos en el modelo
    ];


    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}
