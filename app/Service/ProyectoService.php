<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;
use SebastianBergmann\CodeCoverage\StaticAnalysisCacheNotConfiguredException;

class ProyectoService
{

    private static $urlApi;

    public static function ListaProyectos($sucursal)
    {
        self::$urlApi = ApiUrl::urlApi();

        $response = Http::timeout(60)->get(self::$urlApi . $sucursal . '/ComercialProyecto');
        if ($response->successful()) {
            return $response->json();
        }
        return []; // Devolver un array vacío en caso de error
    }

   
}
