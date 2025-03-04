<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;


class ProveedorService
{

    private static $urlApi;

    public static function ListaProveedores($sucursal)
    {
        self::$urlApi = ApiUrl::urlApi();

        $response = Http::timeout(60)->get(self::$urlApi . $sucursal . '/ComercialProveedor');
        if ($response->successful()) {
            return $response->json();
        }
        return []; // Devolver un array vacío en caso de error
    }
}
