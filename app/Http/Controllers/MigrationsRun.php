<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MigrationsRun extends Controller
{
    public function runMigrations()
    {
        $session = auth()->id();
        /* if ($session === 2) {
            \Illuminate\Support\Facades\Artisan::call('migrate');
            return 'Migraciones ejecutadas correctamente.';
        } else {
            abort(404);
        } */
        if ($session === 2) {
            try {
                \Illuminate\Support\Facades\Artisan::call('cache:clear');
                \Illuminate\Support\Facades\Artisan::call('view:clear');
                \Illuminate\Support\Facades\Artisan::call('config:clear');
                \Illuminate\Support\Facades\Artisan::call('route:clear');
                \Illuminate\Support\Facades\Artisan::call('event:clear');
    
                return 'Todas las cachés han sido limpiadas correctamente.';
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al limpiar las cachés: ' . $e->getMessage()], 500);
            }
        } else {
            abort(404);
        }
       
    }

    public function clearViewCache()
    {

        $session = auth()->id();
        if ($session === 2) {
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            return 'Caché de vistas limpiado correctamente.';
        } else {
            abort(404);
        }
    }
}
