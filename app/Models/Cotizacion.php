<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;
    protected $table = 'cotizaciones';

    protected $fillable = [
        'url',
        'proveedor_id',
        'proveedor',
        'requisicion_id',
        'dias_entrega',
        'dias_credito',
        'formapago',
        'comentarios',
        'estatus'
    ];

    public function detalleCotizaciones()
    {
        return $this->hasMany(DetalleCotizacion::class);
    }

    public function requisicion()
    {
        return $this->belongsTo(Requisicion::class, 'requisicion_id');
    }
}
