<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleRequisicion extends Model
{
    use HasFactory;

    protected $table = 'detalle_requisiciones';


    protected $fillable = [

        'requisicion_id',
        'producto_id',
        'cantidad',
        'producto',
        'observaciones',

    ];
}
