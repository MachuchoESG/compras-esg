<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;
use SebastianBergmann\CodeCoverage\StaticAnalysisCacheNotConfiguredException;

class ProductoService
{

    private static $urlApi;

    public static function ListaProductos($sucursal)
    {
        self::$urlApi = ApiUrl::urlApi();

        $response = Http::get(self::$urlApi . $sucursal . '/ComercialProductos');
        if ($response->successful()) {
            return $response->json();
        }
        return []; // Devolver un array vacío en caso de error
    }

    public static function VerificarExistencia($sucursal, $Producto_id)
    {
        self::$urlApi = ApiUrl::urlApi();

        $response = Http::get(self::$urlApi . $sucursal . '/ComercialExistencia/' . $Producto_id);
        if ($response->successful()) {
            return $response->json();
        }
        return 0;
    }
}
