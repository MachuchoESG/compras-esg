<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();


        \App\Models\Departamento::factory()->create([
            'name' => 'Sistemas'

        ]);

        \App\Models\Puesto::factory()->create([
            'name' => 'Auxiliar de Sistemas',
            'departamento_id' => 1

        ]);

        \App\Models\Puesto::factory()->create([
            'name' => 'Desarrollador',
            'departamento_id' => 1

        ]);

        \App\Models\Puesto::factory()->create([
            'name' => 'Gerente de Sistemas',
            'departamento_id' => 1

        ]);

        \App\Models\User::factory()->create([
            'name' => 'Jesus Gaytan',
            'email' => 'soporte2@esg.com.mx',
            'puesto_id' => 2
        ]);


        \App\Models\User::factory()->create([
            'name' => 'Victor Machucho',
            'email' => 'sistemas@esg.com.mx',
            'puesto_id' => 1
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Abraham Rivera',
            'email' => 'arivera@esg.com.mx',
            'puesto_id' => 3
        ]);

        \App\Models\Estatus::factory()->create([
            'name' => 'Pendiente',

        ]);
        \App\Models\Estatus::factory()->create([
            'name' => 'Cotizacion cargada',

        ]);
        \App\Models\Estatus::factory()->create([
            'name' => 'Autorizada',

        ]);
        \App\Models\Estatus::factory()->create([
            'name' => 'No autorizada',

        ]);
        \App\Models\Estatus::factory()->create([
            'name' => 'Volver a cotizar',

        ]);

        \App\Models\Estatus::factory()->create([
            'name' => 'Finalizada',

        ]);
    }
}
