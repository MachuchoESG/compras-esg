<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autorizacionhistorial extends Model
{
    use HasFactory;

    protected $table = 'autorizacionhistorial';


    protected $fillable = [
        'requisicion_id',
        'user_id',
        'user_solicita',
        'departamento_id',
        'autorizado'

    ];

    public function autorizador()
    {
        return $this->belongsTo(User::class, 'user_id', 'puesto_id');
    }
}
