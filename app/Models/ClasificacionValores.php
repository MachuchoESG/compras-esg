<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class ClasificacionValores extends Model
{
    use HasFactory;

    protected $fillable = [
        'cidvalorclasificacion',
        'cvalorclasificacion'

    ];
}