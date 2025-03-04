<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class UnidadService
{

    private static $urlApi;

    public static function ListaUnidades($sucursal)
    {
        self::$urlApi = ApiUrl::urlApi();

        $response = Http::timeout(60)->get(self::$urlApi . $sucursal . '/Unidad');
        if ($response->successful()) {
            return $response->json();
        }
        return []; // Devolver un array vacío en caso de error
    }
}
