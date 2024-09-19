<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MigrationsRun extends Controller
{
    public function runMigrations()
    {
        $session = auth()->id();
        if ($session === 2) {
            \Illuminate\Support\Facades\Artisan::call('migrate');
            return 'Migraciones ejecutadas correctamente.';
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
