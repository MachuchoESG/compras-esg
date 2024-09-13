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
}
